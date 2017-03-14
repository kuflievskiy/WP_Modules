<style style="text/css">
    .hover-table-wrapper>table tr:hover {
        background-color: #beffac;
    }
</style>

<div class="wrap">
	<h2><?php echo $this->get_plural(); ?></h2>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <div class="hover-table-wrapper">
			<?php $this->prepare_items(); ?>
			<?php $this->display(); ?>
        </div>
    </form>

</div>