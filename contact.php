<?php
$page_title = 'Contact Us';
include 'components/header.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bolton_university'); // Change DB name

$message = '';
if ($_POST) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $msg = htmlspecialchars($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $msg) {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $msg);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Thank you for your message! We will get back to you soon.</div>';
        } else {
            $message = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Error saving your message. Please try again.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Please fill in all fields.</div>';
    }
}
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <p>Get in touch with University of Bolton - we're here to help with your questions and inquiries</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-2">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

                <br><br>
                
                <div class="contact-details" style="margin-bottom: 1.5rem;">
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> Ras Al Kaimah, University of Bolton</p>
                    <p><i class="fas fa-phone"></i> <strong>Phone:</strong> +971 7 2211221</p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> info@bolton.ac.ae</p>
                    <p><i class="fas fa-clock"></i> <strong>Hours:</strong> Mon-Fri 9:00am - 5:00pm</p>
                </div>

            </div>

            <div class="contact-form-container">
                <h2>Send us a Message</h2>
                <?php echo $message; ?>
                
                <form class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="admissions">Admissions Inquiry</option>
                            <option value="academic">Academic Information</option>
                            <option value="technical">Technical Support</option>
                            <option value="general">General Question</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment"></i> Message</label>
                        <textarea id="message" name="message" rows="6" required placeholder="Please describe your inquiry in detail..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Find Us</h2>
        <div class="map-container">
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <p>Interactive map would be embedded here<br>University of Bolton Campus</p>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>