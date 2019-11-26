        <script>
            var base_url = '<?php echo base_url();?>';
            var csrf_name = '<?php echo $this->security->get_csrf_token_name();?>';
        </script>
        <script>window.jQuery || document.write('<script src="<?php echo base_url();?>assets/plugins/jquery/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="<?php echo base_url();?>assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/js.cookie.js"></script>
        <script src="<?php echo base_url();?>assets/dist/js/site.js"></script>

        
        <?php if(!empty($plugin)):?>
            <!-- add js plugin -->
            <?php foreach($plugin as $js):?>
                <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/<?php echo $js;?>"></script>
            <?php endforeach;?>
        <?php endif;?>

        
        <?php if(!empty($custom_js)):?>
            <!-- user defined js -->
            <?php
            $this->view($custom_js['src'],$custom_js['data']); 
            ?>
        <?php endif;?>

    </body>
</html>