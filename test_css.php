<?php
$page_title = 'CSS Test';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <h2>CSS Test Page</h2>
        
        <!-- Test Events Styling -->
        <div class="events-header">
            <h2>Test Events Header</h2>
            <p>This should have a gradient background and gold border</p>
        </div>
        
        <div class="events-grid">
            <div class="event-card">
                <div class="event-header">
                    <h3>Test Event Card</h3>
                    <div class="event-actions">
                        <button class="btn btn-small">Test Button</button>
                    </div>
                </div>
                <div class="event-details">
                    <p><i class="fas fa-calendar"></i> Test Date</p>
                    <p><i class="fas fa-clock"></i> Test Time</p>
                </div>
                <div class="event-description">
                    This is a test event description to verify the styling is working.
                </div>
                <div class="event-stats">
                    <span class="stat-badge">
                        <i class="fas fa-users"></i> 5 registered
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Test Guest Notice -->
        <div class="guest-notice">
            <i class="fas fa-info-circle"></i>
            <p>This is a test guest notice. <a href="#">Login</a> or <a href="#">Register</a> to continue.</p>
        </div>
        
        <!-- Test Registration Stats -->
        <div class="registration-stats">
            <div class="stat-card">
                <div class="stat-number">25</div>
                <div class="stat-label">Total Registrations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15</div>
                <div class="stat-label">Attended</div>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?> 