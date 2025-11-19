<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="main-nav">
    <div class="nav-brand">
        <img src="../../assets/img/admin_icon.png" alt="Strathmore University" class="logo">
        <span>TeachMe Admin</span>
    </div>

    <ul class="nav-links">
        <li>
            <a href="admin_dash.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin_dash.php' ? 'active' : '' ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="manage_tutors.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_tutors.php' ? 'active' : '' ?>">
                Tutors
            </a>
        </li>
        <li>
            <a href="manage_users.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>">
                Users
            </a>
        </li>
        <li>
            <a href="manage_sessions.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_sessions.php' ? 'active' : '' ?>">
                Sessions
            </a>
        </li>
        <li>
            <a href="manage_feedback.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_feedback.php' ? 'active' : '' ?>">
                Feedback
            </a>
        </li>
        <li>
            <a href="certificates.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'certificates.php' ? 'active' : '' ?>">
                Certificates
            </a>
        </li>
        <li>
            <a href="reports.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                Reports
            </a>
        </li>
        <li class="nav-user">
            <span class="user-info">
                <img src="../../assets/img/admin_icon.png" alt="Admin" class="user-icon">
                <span class="user-details">
                    <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>
                    <small>Administrator</small>
                </span>
            </span>
            <div class="user-dropdown">
                <a href="admin_dash.php" class="dropdown-item">Dashboard</a>
                <a href="manage_tutors.php" class="dropdown-item">Manage Tutors</a>
                <a href="manage_users.php" class="dropdown-item">Manage Users</a>
                <a href="manage_sessions.php" class="dropdown-item">Manage Sessions</a>
                <a href="manage_feedback.php" class="dropdown-item">Manage Feedback</a>
                <a href="certificates.php" class="dropdown-item">Certificates</a>
                <a href="reports.php" class="dropdown-item">Reports</a>
                <div class="dropdown-divider"></div>
                <a href="../auth/logout.php" class="dropdown-item logout">Logout</a>
            </div>
        </li>
    </ul>

    <div class="nav-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

<style>
/* Navigation Styles */
.main-nav {
    background: #2c3e50;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: 70px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-brand .logo { 
    height: 40px; 
    width: auto; 
    border-radius: 50%;
}
.nav-brand span { 
    font-size: 1.2rem; 
    font-weight: bold; 
    color: white; 
}

.nav-links { 
    display: flex; 
    list-style: none; 
    gap: 1.2rem; 
    margin: 0; 
    padding: 0; 
    align-items: center; 
    flex-wrap: wrap;
}
.nav-link { 
    color: white; 
    text-decoration: none; 
    padding: 1rem 0.5rem; 
    font-weight: 500; 
    position: relative; 
    transition: all 0.3s; 
    font-size: 0.9rem;
    white-space: nowrap;
}
.nav-link:hover { 
    color: #3498db; 
    transform: translateY(-2px); 
}
.nav-link.active { 
    color: #3498db; 
    font-weight: 600; 
}
.nav-link.active::after { 
    content: ''; 
    position: absolute; 
    bottom: 0; 
    left: 0; 
    right: 0; 
    height: 3px; 
    background: #3498db; 
    border-radius: 2px; 
}

.nav-user { 
    position: relative; 
    cursor: pointer; 
    margin-left: 1rem; 
}
.user-info { 
    display: flex; 
    align-items: center; 
    gap: 0.75rem; 
    padding: 0.5rem 1rem; 
    background: rgba(255,255,255,0.1); 
    border-radius: 25px; 
    transition: background 0.3s; 
}
.user-info:hover { 
    background: rgba(255,255,255,0.2); 
}

.user-icon { 
    height: 35px; 
    width: 35px; 
    border-radius: 50%; 
    border: 2px solid rgba(255,255,255,0.3); 
}
.user-details { 
    display: flex; 
    flex-direction: column; 
    line-height: 1.2; 
}
.user-details strong { 
    font-size: 0.9rem; 
}
.user-details small { 
    font-size: 0.75rem; 
    opacity: 0.8; 
}

.user-dropdown { 
    position: absolute; 
    top: 100%; 
    right: 0; 
    background: white; 
    color: #333; 
    min-width: 220px; 
    border-radius: 10px; 
    opacity: 0; 
    visibility: hidden; 
    transform: translateY(-10px); 
    transition: all 0.3s; 
    z-index: 1001; 
    border: 1px solid #e1e8ed; 
}
.nav-user:hover .user-dropdown { 
    opacity: 1; 
    visibility: visible; 
    transform: translateY(5px); 
}

.dropdown-item { 
    display: flex; 
    align-items: center; 
    gap: 0.75rem; 
    padding: 0.875rem 1rem; 
    color: #333; 
    text-decoration: none; 
    border-bottom: 1px solid #f8f8f8; 
    transition: background 0.3s; 
    font-size: 0.9rem;
}
.dropdown-item:last-child { 
    border-bottom: none; 
}
.dropdown-item:hover { 
    background: #f8f8f8; 
    color: #2c3e50; 
}
.dropdown-divider { 
    height: 1px; 
    background: #e9ecef; 
    margin: 0.5rem 0; 
}
.logout { 
    color: #e74c3c; 
    font-weight: 500; 
}
.logout:hover { 
    background: #ffe6e6; 
    color: #c0392b; 
}

.nav-toggle { 
    display: none; 
    flex-direction: column; 
    cursor: pointer; 
    padding: 0.5rem; 
}
.nav-toggle span { 
    width: 25px; 
    height: 3px; 
    background: white; 
    margin: 3px 0; 
    border-radius: 2px; 
    transition: 0.3s; 
}

/* Mobile Responsive */
@media (max-width: 1024px) {
    .nav-links { 
        gap: 0.8rem; 
    }
    .nav-link { 
        font-size: 0.85rem; 
        padding: 1rem 0.3rem; 
    }
}

@media (max-width: 768px) {
    .nav-links { 
        display: none; 
        flex-direction: column; 
        position: absolute; 
        top: 100%; 
        left: 0; 
        right: 0; 
        background: #2c3e50; 
        padding: 1rem 0; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
        gap: 0;
    }
    .nav-links.active { 
        display: flex; 
    }
    .nav-toggle { 
        display: flex; 
    }
    .nav-user { 
        margin: 0; 
        width: 100%; 
    }
    .user-info { 
        justify-content: center; 
        background: transparent; 
    }
    .user-dropdown { 
        position: static; 
        opacity: 1; 
        visibility: visible; 
        transform: none; 
        box-shadow: none; 
        background: rgba(255,255,255,0.05); 
        border: none; 
        margin-top: 0.5rem; 
    }
    .dropdown-item { 
        color: white; 
        border-bottom-color: rgba(255,255,255,0.1); 
    }
    .dropdown-item:hover { 
        background: rgba(255,255,255,0.1); 
        color: white; 
    }
    .nav-link {
        padding: 0.8rem 1rem;
        width: 100%;
        text-align: center;
        font-size: 1rem;
    }
}

/* Nav animation */
.nav-link { 
    animation: fadeInUp 0.5s ease-out; 
}
@keyframes fadeInUp { 
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    } 
    to { 
        opacity: 1; 
        transform: translateY(0); 
    } 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    if(navToggle){
        navToggle.addEventListener('click', function(){
            navLinks.classList.toggle('active');
            const spans = navToggle.querySelectorAll('span');
            if(navLinks.classList.contains('active')){
                spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(7px,-6px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }

    document.addEventListener('click', function(event){
        if(!event.target.closest('.nav-user')){
            document.querySelectorAll('.user-dropdown').forEach(dropdown=>{
                dropdown.style.opacity='0';
                dropdown.style.visibility='hidden';
                dropdown.style.transform='translateY(-10px)';
            });
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.main-nav') && navLinks.classList.contains('active')) {
            navLinks.classList.remove('active');
            const spans = navToggle.querySelectorAll('span');
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    });
});
</script>