<?php
$page_title = 'News';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-newspaper"></i> University News</h1>
        <p>Stay updated with the latest news, events, and announcements from University of Bolton</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-2">
            <article class="news-card">
                <div class="news-image">
                    <img src="Images/campus.png" alt="New Academic Year">
                    <div class="news-date">
                        <span class="day">15</span>
                        <span class="month">Jan</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>New Academic Year 2024 Begins</h3>
                    <p>University of Bolton welcomes over 2,000 new students for the 2024 academic year. Our expanded programs and state-of-the-art facilities are ready to provide exceptional education.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Admin</span>
                        <span><i class="fas fa-tag"></i> Academic</span>
                    </div>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="Images/library.png" alt="Research Grant">
                    <div class="news-date">
                        <span class="day">10</span>
                        <span class="month">Jan</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>£2M Research Grant Awarded</h3>
                    <p>Our Engineering department has been awarded a prestigious £2 million research grant to develop sustainable energy solutions for the future.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Research Team</span>
                        <span><i class="fas fa-tag"></i> Research</span>
                    </div>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="Images/garden.png" alt="Student Achievement">
                    <div class="news-date">
                        <span class="day">05</span>
                        <span class="month">Jan</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>Students Win National Competition</h3>
                    <p>Our Computer Science students have won first place in the National Programming Championship, showcasing their exceptional skills and dedication.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Student Affairs</span>
                        <span><i class="fas fa-tag"></i> Achievement</span>
                    </div>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="Images/cafteria.png" alt="New Facilities">
                    <div class="news-date">
                        <span class="day">01</span>
                        <span class="month">Jan</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>New Sports Complex Opens</h3>
                    <p>The new £5 million sports complex is now open, featuring modern gym equipment, swimming pool, and multi-purpose courts for all students.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Facilities</span>
                        <span><i class="fas fa-tag"></i> Infrastructure</span>
                    </div>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="Images/campus2.png" alt="Partnership">
                    <div class="news-date">
                        <span class="day">28</span>
                        <span class="month">Dec</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>Industry Partnership Announced</h3>
                    <p>University of Bolton partners with leading tech companies to provide internship opportunities and real-world experience for our students.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Career Services</span>
                        <span><i class="fas fa-tag"></i> Partnership</span>
                    </div>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="Images/bolton_logo.png" alt="Graduation">
                    <div class="news-date">
                        <span class="day">20</span>
                        <span class="month">Dec</span>
                    </div>
                </div>
                <div class="news-content">
                    <h3>Winter Graduation Ceremony</h3>
                    <p>Congratulations to all our graduates! The winter graduation ceremony celebrated the achievements of over 500 students across various disciplines.</p>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Academic Office</span>
                        <span><i class="fas fa-tag"></i> Graduation</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>