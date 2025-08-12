<?php
$page_title = 'About Us';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section-title">About University of Bolton</h1>
        
        <div class="grid grid-2" style="margin-bottom: 3rem;">
            <div>
                <h2 style="color: #ffd700; margin-bottom: 1rem;">Our History</h2>
                <p style="margin-bottom: 1rem;">Founded in 1982, the University of Bolton has been at the forefront of higher education for over four decades. What began as a small technical college has evolved into a prestigious university known for its innovative programs and commitment to student success.</p>
                <p style="margin-bottom: 1rem;">Our institution has consistently adapted to meet the changing needs of students and industry, ensuring that our graduates are well-prepared for the challenges of the modern world.</p>
            </div>
            <div>
                <img src="Images/campus.png" alt="University Campus" style="width: 100%; border-radius: 8px;">
            </div>
        </div>

        <div class="grid grid-3">
            <div class="card">
                <h3><i class="fas fa-eye"></i> Our Vision</h3>
                <p>To be a leading university that transforms lives through exceptional education, innovative research, and meaningful community engagement.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                <p>To provide high-quality education that empowers students to achieve their full potential and make positive contributions to society.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-heart"></i> Our Values</h3>
                <ul style="list-style: none; padding: 0;">
                    <li><i class="fas fa-check" style="color: #ffd700;"></i> Excellence in Education</li>
                    <li><i class="fas fa-check" style="color: #ffd700;"></i> Innovation and Creativity</li>
                    <li><i class="fas fa-check" style="color: #ffd700;"></i> Diversity and Inclusion</li>
                    <li><i class="fas fa-check" style="color: #ffd700;"></i> Community Engagement</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Key Statistics</h2>
        <div class="grid grid-3">
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 3rem; color: #ffd700;">15,000+</h3>
                <p>Active Students</p>
            </div>
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 3rem; color: #ffd700;">500+</h3>
                <p>Expert Faculty</p>
            </div>
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 3rem; color: #ffd700;">100+</h3>
                <p>Programs Offered</p>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>