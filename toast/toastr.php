<?php
if (!empty($message)): ?>
    <script type="text/javascript">
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        <?php if ($message_type == 'success'): ?>
            toastr.success('<?php echo addslashes($message); ?>', 'Success');
        <?php elseif ($message_type == 'error'): ?>
            toastr.error('<?php echo addslashes($message); ?>', 'Error');
        <?php endif; ?>
    </script>
<?php endif; ?>