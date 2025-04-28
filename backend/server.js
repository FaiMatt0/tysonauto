const express = require('express');
const nodemailer = require('nodemailer');
const bodyParser = require('body-parser');
const cors = require('cors');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware - more permissive for testing
app.use(cors({
  origin: '*', // Allow all origins during testing
  methods: ['POST', 'GET', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));

// Parse JSON bodies
app.use(bodyParser.json());
app.use(express.urlencoded({ extended: true }));

// Serve static files if needed
app.use(express.static('public'));

// Debug endpoint to verify form data
app.post('/debug-form', (req, res) => {
  console.log('DEBUG FORM DATA:');
  console.log(JSON.stringify(req.body, null, 2));
  
  res.status(200).json({
    message: 'Form data received successfully',
    data: req.body
  });
});

// Simple endpoint for the contact form (no rate limiting for testing)
app.post('/send-email', async (req, res) => {
  console.log('FORM SUBMISSION RECEIVED:');
  console.log(JSON.stringify(req.body, null, 2));
  
  try {
    // Get original data
    let { name, email, phone, subject, message, privacy } = req.body;

    // Basic validation
    if (!name) {
      return res.status(400).json({ error: 'Nome è obbligatorio' });
    }

    // Log the exact format of the data we're receiving
    console.log('Email data as received:');
    console.log(`- Name: "${name}"`);
    console.log(`- Email: "${email}"`);
    console.log(`- Phone: "${phone}"`);
    console.log(`- Subject: "${subject}"`);
    console.log(`- Message: "${message}"`);
    
    // PATTERN DETECTION - Fix the exact pattern you're experiencing
    // If email contains just numbers and message contains an email address, swap them
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9+\s()-]{5,20}$/;
    
    // Check for specific pattern: numeric email + email-looking message
    if (email && phoneRegex.test(email) && message && emailRegex.test(message)) {
      console.log('DETECTED PATTERN: Email field contains phone number, message contains email');
      // Move the email address from message to email field
      const correctEmail = message;
      // Move phone number from email field to phone field
      const correctPhone = email;
      // Clear the message field
      message = '';
      
      // Update the values
      email = correctEmail;
      phone = correctPhone;
      
      console.log('CORRECTED DATA:');
      console.log(`- Email: "${email}"`);
      console.log(`- Phone: "${phone}"`);
      console.log(`- Message: "${message}"`);
    }
    
    // Additional check: If email still doesn't look like valid email, try to find it elsewhere
    if (!emailRegex.test(email)) {
      // Check if phone contains an email
      if (phone && emailRegex.test(phone)) {
        console.log('Found email in phone field, swapping...');
        const temp = email;
        email = phone;
        phone = temp;
      } 
      // Check if message contains an email pattern
      else if (message) {
        const emailMatch = message.match(/[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/);
        if (emailMatch) {
          console.log('Found email in message, extracting...');
          const extractedEmail = emailMatch[0];
          message = message.replace(extractedEmail, '').trim();
          
          // If the current email field contains a phone number, save it
          if (phoneRegex.test(email)) {
            phone = email;
          }
          
          email = extractedEmail;
        }
      }
    }

    // Default subject if not provided
    subject = subject || 'Messaggio dal sito web';
    
    // Create transporter with same settings as test-email.js
    const transporter = nodemailer.createTransport({
      host: process.env.EMAIL_HOST,
      port: process.env.EMAIL_PORT,
      secure: process.env.EMAIL_PORT === '465',
      auth: {
        user: process.env.EMAIL_USER,
        pass: process.env.EMAIL_PASS,
      },
      tls: {
        rejectUnauthorized: false
      },
      debug: true // Show debug output
    });

    // Verify connection first
    console.log('Verifying SMTP connection...');
    await transporter.verify();
    console.log('SMTP connection verified');
    
    const mailOptions = {
      from: `"Modulo di Contatto" <${process.env.EMAIL_USER}>`,
      to: process.env.EMAIL_RECEIVER,
      subject: `Messaggio da ${name} - ${subject}`,
      text: `
        Nome: ${name}
        Email: ${email || 'Non specificato'}
        Telefono: ${phone || 'Non specificato'}
        Oggetto: ${subject || 'Non specificato'}
        Messaggio:
        ${message || 'Nessun messaggio'}
      `,
      html: `
<div style="font-family: Arial, sans-serif; padding: 20px;">
  <h2>Nuovo messaggio dal sito web</h2>
  <p><strong>Nome:</strong> ${name}</p>
  <p><strong>Email:</strong> ${email || 'Non specificato'}</p>
  <p><strong>Telefono:</strong> ${phone || 'Non specificato'}</p>
  <p><strong>Oggetto:</strong> ${subject || 'Non specificato'}</p>
  <h3>Messaggio:</h3>
  <p>${(message || 'Nessun messaggio').replace(/\n/g, '<br>')}</p>
</div>
      `
    };

    // Send email with more detailed logging
    console.log('Sending email to:', process.env.EMAIL_RECEIVER);
    console.log('From:', process.env.EMAIL_USER);
    const info = await transporter.sendMail(mailOptions);
    
    console.log('Email sent successfully');
    console.log('Message ID:', info.messageId);
    
    // Return success response
    res.status(200).json({ 
      message: 'Email inviata con successo!',
      messageId: info.messageId
    });
    
  } catch (error) {
    console.error('ERROR SENDING EMAIL:');
    console.error(error);
    
    res.status(500).json({ 
      error: 'Errore durante l\'invio dell\'email',
      details: error.message,
      stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
    });
  }
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.status(200).json({ 
    status: 'ok', 
    timestamp: new Date().toISOString(),
    env: {
      EMAIL_HOST: process.env.EMAIL_HOST,
      EMAIL_PORT: process.env.EMAIL_PORT,
      EMAIL_USER: process.env.EMAIL_USER,
      EMAIL_RECEIVER: process.env.EMAIL_RECEIVER,
      NODE_ENV: process.env.NODE_ENV || 'development'
    }
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
  console.log('Email configuration:');
  console.log('- HOST:', process.env.EMAIL_HOST);
  console.log('- PORT:', process.env.EMAIL_PORT);
  console.log('- USER:', process.env.EMAIL_USER);
  console.log('- RECEIVER:', process.env.EMAIL_RECEIVER);
});