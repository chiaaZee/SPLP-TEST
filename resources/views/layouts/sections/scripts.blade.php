<!-- BEGIN: Vendor JS-->

@vite([
'resources/assets/vendor/libs/jquery/jquery.js',
'resources/assets/vendor/libs/popper/popper.js',
'resources/assets/vendor/js/bootstrap.js',
'resources/assets/vendor/libs/node-waves/node-waves.js',
'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
'resources/assets/vendor/libs/hammer/hammer.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<!-- Table Dropdown Stacking and Overflow Fix -->
<script>
document.addEventListener('show.bs.dropdown', function (event) {
    var dropdown = event.target.closest('.dropdown, .btn-group');
    if (dropdown) {
        dropdown.style.zIndex = '1050';
        dropdown.style.position = 'relative';
        
        var tableResponsive = dropdown.closest('.table-responsive');
        if (tableResponsive) {
            tableResponsive.style.overflow = 'visible';
        }
        var card = dropdown.closest('.card');
        if (card) {
            card.style.overflow = 'visible';
        }
        
        var row = dropdown.closest('tr, .animate__animated');
        if (row) {
            row.style.zIndex = '1050';
            row.style.position = 'relative';
        }
    }
});

document.addEventListener('hide.bs.dropdown', function (event) {
    var dropdown = event.target.closest('.dropdown, .btn-group');
    if (dropdown) {
        dropdown.style.zIndex = '';
        dropdown.style.position = '';
        
        var tableResponsive = dropdown.closest('.table-responsive');
        if (tableResponsive) {
            tableResponsive.style.overflow = '';
        }
        var card = dropdown.closest('.card');
        if (card) {
            card.style.overflow = '';
        }
        
        var row = dropdown.closest('tr, .animate__animated');
        if (row) {
            row.style.zIndex = '';
            row.style.position = '';
        }
    }
});
</script>

