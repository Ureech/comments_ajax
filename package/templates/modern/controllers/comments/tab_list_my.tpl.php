<?php $this->addCSS($this->getTplFilePath('controllers/comments/css/rating.css', false)); ?>

<div id="comments_widget" class="tabs-menu icms-comments__tabs">
    <span id="comments"></span>
    <ul class="nav nav-tabs <?php echo $target_controller . '_' . $target_subject; ?>_comments_tab position-relative">
        <?php foreach ($comment_systems as $key => $comment_system) { ?>
            <li class="nav-item">
                <a href="#tab-<?php echo $comment_system['name']; ?>" class="nav-link <?php if (!$key) { ?>active<?php } ?>" data-toggle="tab" role="tab">
                    <?php echo $comment_system['title']; ?>
                </a>
                <?php if (!empty($comment_system['icon'])) { ?>
                    <a href="<?php echo $comment_system['icon']['href']; ?>" class="icms-comments__tabs-tab btn <?php echo $comment_system['icon']['class']; ?>" title="<?php echo $comment_system['icon']['title']; ?>">
                        <?php html_svg_icon('solid', $comment_system['icon']['icon']); ?>
                    </a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <div id="ajax_html" class="tab-content">
        <?php foreach ($comment_systems as $key => $comment_system) { ?>
            <div id="tab-<?php echo $comment_system['name']; ?>" class="tab-pane<?php if (!$key) { ?> show active<?php } ?> <?php echo $target_controller . '_' . $target_subject; ?>_comments" role="tablist">

            </div>
        <?php } ?>
        
    </div>
</div>

<?php ob_start();
?>
  
<?php $this->addJS($this->getTplFilePath('js/comments.js', false)); ?>
<?php $this->addJS($this->getTplFilePath('controllers/comments/js/newcomm.js', false)); ?>
<?php $this->addCSS($this->getTplFilePath('controllers/comments/css/newcomm.css', false)); ?>
<script>

    <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_COMMENT_DELETED', 'LANG_COMMENT_DELETE_CONFIRM', 'LANG_MODERATION_REFUSE_REASON'); ?>
    <?php if ($is_highlight_new) { ?>icms.comments.showFirstSelected();
    <?php } ?>
    icms.comments.init({
        get: '<?php echo $this->href_to('get'); ?>',
        approve: '<?php echo $this->href_to('approve'); ?>',
        delete: '<?php echo $this->href_to('delete'); ?>',
        refresh: '<?php echo $this->href_to('refresh'); ?>',
        track: '<?php echo $this->href_to('track'); ?>',
        rate: '<?php echo $this->href_to('rate'); ?>'
    }, {
        tc: '<?php echo $target_controller; ?>',
        ts: '<?php echo $target_subject; ?>',
        ti: '<?php echo $target_id; ?>',
        tud: '<?php echo $target_user_id; ?>',
        timestamp: '<?php echo time(); ?>'
    });

    icms.newcomm.Html(
        '<?php echo $number_page; ?>',
        '<?php echo $target_subject ?>',
        '<?php echo $target_controller ?>',
        '<?php echo $target_id ?>',
        '<?php echo $target_user_id ?>'
    );
    icms.newcomm.init({
        like: '<?php echo $this->href_to('like'); ?>',
        score: '<?php echo $this->href_to('score'); ?>',
        
    },{
        subject: '<?php echo $target_subject; ?>',
        controller: '<?php echo $target_controller; ?>',
        count: '<?php echo $comments_count; ?>',
    });
    
</script>
<?php $this->addBottom(ob_get_clean());?>
