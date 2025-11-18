if (!isset($_SESSION)) {
    session_start();
}
?>

<footer class="main-footer" style="
    margin-top:auto; 
    padding:20px; 
    background:#003366; 
    color:#fff; 
    text-align:center; 
    font-size:14px;
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    z-index:1000;
">
    <p>&copy; <?php echo date('Y'); ?> TeachMe Platform | All Rights Reserved</p>
</footer>

<!-- Optional custom CSS -->
<link rel="stylesheet" href="../../assets/css/tutor.css">

<!-- Global Scripts -->
<script src="/assets/js/main.js"></script>
<script src="/assets/js/notification.js"></script>
<script src="/assets/js/validation.js"></script>

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>