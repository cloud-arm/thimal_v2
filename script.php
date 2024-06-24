<!-- jQuery 2.2.3 -->
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="../../plugins/select2/select2.full.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Dark Theme Btn-->
<script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>

<script>
    function open_profile(val) {
        if (val == 1) {
            $('.dropdown.user.user-menu').toggleClass('open');
            $('.user-data-header').toggleClass('d-none');
            $('.profile-data-header').toggleClass('d-none');
            $('.user-data').removeClass('d-none');
            $('.profile-data').addClass('d-none');
        }

        if (val == 2) {
            $('.user-data').addClass('d-none');
            $('.profile-data').removeClass('d-none');
        }

        if (val == 3) {
            $('.user-data').removeClass('d-none');
            $('.profile-data').addClass('d-none');
        }
    }

    $(function() {
        //Initialize Select2 Elements
        $(".select2").select2();
        $('.select2.hidden-search').select2({
            minimumResultsForSearch: -1
        });
    });
</script>