<?php
$image_folder = wpadc()->plugin_url("assets/img/help/");
?>
<p>Thank you so much for download <strong>WP Affiliate Disclosure</strong>. Before you begin setting up and running this plugin, I would highly recommend you to spend some time go through all the tutorials listed below.</p>
<p>Each tutorials comes with illustrative screenshots and comprehensive explanations, and is designed to be read "step-by-step", from start to finish.</p>

<div class="wpadc-tutorial-wrapper">

<div class="wpadc-tutorial-section">
    <h3 class="wpadc-tutorial-heading">1. Create Your First Rule</h3>
    
    <p>First, simply login to your <code>Admin Dashboard</code>, and look for <code>WP Affiliate Disclosure</code>.</p>

    <p><img src="<?php echo $image_folder; ?>create_1.jpg" class="wpadc-tutorial-image" /></p>

    <p>To add your first rule, simply click the <code>Create New Rule</code> button.</p>
    <p><img src="<?php echo $image_folder; ?>create_2.jpg" class="wpadc-tutorial-image" /></p>

    <p>Next, insert a name, and then hit <code>Create</code> button.</p>
    <p><img src="<?php echo $image_folder; ?>create_3.jpg" class="wpadc-tutorial-image" /></p>

    <p>Once a new rule has been created, hit the <code>Edit</code> button to start edit your first rule settings.</p>
    <p><img src="<?php echo $image_folder; ?>create_4.jpg" class="wpadc-tutorial-image" /></p>

</div><!-- .wpadc-tutorial-section -->

<div class="wpadc-tutorial-section">
    <h3 class="wpadc-tutorial-heading">2. Setting up Your First Rule</h3>
    
    <p>The <code>Edit Rule</code> page is splitted to four sections: <code>General</code>, <code>Disclosure Statement</code>, <code>Conditions</code>, <code>Priority</code>.</p>

    <p><strong>General Section</strong></p>
    <p><img src="<?php echo $image_folder; ?>setting_1.jpg" class="wpadc-tutorial-image" /></p>
    <p>In this section, you can change the name of the rule. This is for your reference only so you can name it whatever you want.</p>

    <p><strong>Disclosure statement Section</strong></p>
    <p><img src="<?php echo $image_folder; ?>setting_2_free.jpg" class="wpadc-tutorial-image" /></p>
    <p>In this section, you can change the content of the statement, as well as where the statement will be display.</p>
    <p>Here you can add links, images, as well as HTML elements into the disclosure statement</p>

    <p><strong>Conditions Section</strong></p>
    <p><img src="<?php echo $image_folder; ?>setting_3_free.jpg" class="wpadc-tutorial-image" /></p>

    <p>Next, you'll need to setup a condition for this rule - so that the system will know when to display the disclosure statement.</p>

    <p>First, you'll need to select the <code>Post Type</code>, then the <code>Post Type Condition</code>.</p>

    <p>By default, it's set to show on all posts. However, you can set it either on a specific taxonomies (category or tag), or by specific post(s).</p>

    <p><strong>Only show on selected taxonomies ( categories or tags )</strong></p>

    <p>If you want the statement to only show on a selected categories or tags, you'll need to select <code>Only Show on Selected Taxonomies (categories / tags)</code> option on the <code>Post Type Condition</code> field.</p>

    <p><img src="<?php echo $image_folder; ?>setting_4.jpg" class="wpadc-tutorial-image" /></p>

    <p>Then, enter the category (or tag) slug into the input box. If you have multiple categories, you'll need to separate the category slugs by comma. For example: slug-1,slug-2,slug-3</p>

    <p><strong>Only show on selected posts</strong></p>

    <p>If you want the statement to only show on a selected posts, you'll need to select <code>Only Show on Selected Post(s)</code> option on the <code>Post Type Condition</code> field.</p>

    <p><img src="<?php echo $image_folder; ?>setting_5.jpg" class="wpadc-tutorial-image" /></p>

    <p>Then, enter the post id into the input box. If you have multiple post(s), you'll need to separate the post id by comma. For example: 111,234,314</p>

    <p><strong>Priority Section</strong></p>

    <p>Here you can set up the rule's priority.</p>

    <p><img src="<?php echo $image_folder; ?>setting_6.jpg" class="wpadc-tutorial-image" /></p>

    <p>Please note that the system will only show one statement at a time - meaning that if a particular post meet the condition of two different rules - only one rule will be used. So, the rule priority is use to determine which rule's disclosure statement should be displayed.</p>

    <p>The lower the number entered, the higher the priority will be given.</p>

    <p>For example, rule A has a priority of 1, and rule B has a priority of 2, and both rules meet the conditions for a particular post. Only the disclosure statement from rule A will be displayed (since it has the higher priority), and rule B will be ignored.</p>

</div><!-- .wpadc-tutorial-section -->

</div><!-- .wpadc-tutorial-wrapper -->

<script type="text/javascript">
jQuery(document).ready(function(){ 
    
});
</script>