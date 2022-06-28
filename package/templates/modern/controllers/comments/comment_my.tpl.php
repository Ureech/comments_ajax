<?php
$limit_nesting = !empty($this->controller->options['limit_nesting']) ? $this->controller->options['limit_nesting'] : 0;
$dim_negative = !empty($this->controller->options['dim_negative']);
$is_highlight_new = isset($is_highlight_new) ? $is_highlight_new : false;
if (!isset($is_can_rate)) {
    $is_can_rate = false;
}
$user_model = cmsCore::getModel('users');
?>

<?php $this->addCSS($this->getTplFilePath('controllers/comments/css/newcomm.css', false)); ?>
<?php $this->addJS($this->getTplFilePath('controllers/comments/js/newcomm.js', false)); ?>

<?php foreach ($comments as $entry) {

    $id =  $entry['id'];
    $no_approved_class = $entry['is_approved'] ? '' : 'no_approved';

    $author_url = href_to_profile($entry['user']);

    if ($is_show_target) {
        $target_url = rel_to_href($entry['target_url']) . "#comment_{$entry['id']}";
    }

    $is_selected = $is_highlight_new && (strtotime($entry['date_pub']) > strtotime($user->date_log));

    $level = 0;
    if ($is_levels) {
        $level = (($limit_nesting && $entry['level'] > $limit_nesting) ? $limit_nesting : ($entry['level'] - 1));
    }

    $like_perm = $this->controller->likesPerm($entry);
    $liked     = $this->controller->liked($entry);

    $sum_likes = $this->controller->model->filterEqual('comment_id', $entry['id'])->getCount('comments_likes_log', 'id');

    $this->controller->model->filterEqual('comment_id', $entry['id']);
    $log = $this->controller->model->get('comments_likes_log');
    $i = 0;
?>

    <div data-toggle="" id="comment_<?php echo $entry['id']; ?>" class="media my-3 my-lg-4 comment<?php if ($is_selected) { ?> selected-comment shadow<?php } ?> icms-comments-ns ns-<?php echo $level; ?>" data-level="<?php echo $entry['level']; ?>">

        <?php
        if ($is_rating_newcomm) {
            echo $this->renderChild('comments_rating', array(
                'entry' => $entry, 'user' => $user, 'is_can_rate' => $is_can_rate, 'is_rating_newcomm' => $is_rating_newcomm
            ));
        } ?>


        <div class="media-body">
            <h6 class="d-md-flex align-items-center mb-1">
                <span class="d-none d-sm-inline-block mr-2">
                    <?php if ($entry['user_id']) { ?>
                        <a href="<?php echo $author_url; ?>" class="icms-user-avatar <?php if (!empty($entry['user']['is_online'])) { ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                            <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                        </a>
                    <?php } else { ?>
                        <span class="icms-user-avatar">
                            <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                        </span>
                    <?php } ?>
                </span>
                <?php if ($entry['user_id']) { ?>
                    <a href="<?php echo $author_url; ?>" class="user <?php if ($entry['user_id'] && $target_user_id == $entry['user_id']) { ?>btn btn-success btn-sm border-0<?php } ?>"><?php echo $entry['user']['nickname']; ?></a>
                <?php } else { ?>
                    <span class="guest_name user"><?php echo $entry['author_name']; ?></span>
                    <?php if ($user->is_admin && !empty($entry['author_ip'])) { ?>
                        <span class="guest_ip">
                            [<?php echo $entry['author_ip']; ?>]
                        </span>
                    <?php } ?>
                <?php } ?>

                <?php if (empty($entry['hide_date'])) { ?>
                    <small class="text-muted ml-2">
                        <?php html_svg_icon('solid', 'history'); ?>
                        <span class="<?php echo $no_approved_class; ?>">
                            <?php echo string_date_age_max($entry['date_pub'], true); ?>
                        </span>
                        <?php if ($no_approved_class) { ?>
                            <span class="hide_approved ml-2">
                                <?php echo html_bool_span(LANG_CONTENT_NOT_APPROVED, false); ?>
                            </span>
                        <?php } ?>
                    </small>
                <?php } ?>

                <?php if ($is_controls) { ?>
                    <a data-toggle="tooltip" data-placement="top" href="#comment_<?php echo $entry['id']; ?>" class="text-dark ml-2 mr-4" name="comment_<?php echo $entry['id']; ?>" title="<?php html(LANG_COMMENT_ANCHOR); ?>">#</a>
                <?php } ?>
            </h6>

            <?php if (!$entry['is_deleted'] && !empty($entry['hide_controls'])) { ?>
                <?php echo $entry['content_html']; ?>
            <?php } else { ?>
                <div class="icms-comment-html text-break">

                    <?php echo $entry['content_html']; ?>

                    <h6 id="h6_<?php echo $entry['id']; ?>" class="icons">

                        <?php if ($is_likes_newcomm) { ?>
                            <?php if ($like_perm) { ?>
                                <a onclick="icms.newcomm.addLike('<?php echo $entry['id']; ?>','<?php echo $user->id; ?>','<?php echo $entry['user_id']; ?>','<?php echo $entry['author_name']; ?>')" href="#like" id="<?php echo $entry['id']; ?>" class="icon hover">
                                    <?php html_svg_icon('solid', 'heart'); ?>
                                    <span><?php echo $sum_likes; ?></span>
                                </a>
                            <?php } else { ?>
                                <span id="<?php echo $entry['id']; ?>" class="icon <?php if ($liked) { ?>red<?php } ?>">
                                    <?php html_svg_icon('solid', 'heart'); ?> <use><?php echo $sum_likes; ?></use>
                                </span>
                            <?php } ?>

                            <?php if ($log) { ?>
                                <div class="list_likers container-fluid" id="likers_<?php echo $entry['id'] ?>">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <?php foreach ($log as $val) {
												if($i>2)continue;
                                                    $usr = $user_model->getUser($val['user_id']); ?>
                                                    <div class="users-liked col-3">
                                                        <?php if ($usr['id']) { ?>
                                                            <a class="yes-user" href="/users/<?php echo $val['user_id'] ?>"> <?php echo html_avatar_image($usr['avatar'], 'micro', $usr['nickname']); ?>
                                                                <p><?php echo html_strip($usr['nickname'], 5); ?></p>
                                                            </a>
                                                        <?php }
                                                        if ($val['author_name']) { ?>
                                                            <span class="no-user"> <img class="img-fluid" src="/upload/default/avatar_micro.png" /></span>
                                                            <p><?php echo $val['author_name'] == 'goust' ? 'Гость' : html_strip($val['author_name'], 5); ?></p>
                                                        <?php } ?>
                                                    </div>
                                                <?php $i++; } ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <a class="show-list" onclick="icms.newcomm.allList('<?php echo $id; ?>');" href="javascript:;">Показать весь список</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                        <?php } ?>

                        <?php if (!$entry['is_deleted'] && empty($entry['hide_controls'])) { ?>
                            <div class="icms-comment-controls mt-1">
                                <?php if (!$entry['is_approved']) { ?>
                                    <a class="btn btn-sm btn-outline-success approve hide_approved border-0" href="#approve" title="" onclick="return icms.comments.approve('<?php echo $id; ?>')"><?php html_svg_icon('solid', 'check'); echo LANG_COMMENTS_APPROVE ?></a>
                                <?php } ?>
                                <a class="btn btn-sm  btn-link reply" href="#reply" title="" onclick="return icms.comments.add('<?php echo $id; ?>')"><?php echo LANG_REPLY ?></a>
                                <?php if ($user->is_admin) { ?>
                                    <a class="btn btn-sm border-0 btn-outline-danger delete" href="#delete" title="" onclick="return icms.comments.remove('<?php echo $id; ?>')">
                                        <?php html_svg_icon('solid', 'trash'); ?></a>
                                <?php } ?>
                                <?php if ($is_controls) { ?>
                                    <?php if ($entry['parent_id']) { ?>
                                        <a href="#up" class="btn btn-sm border-0 scroll-up ml-2" onclick="return icms.comments.up(<?php echo $entry['parent_id']; ?>, <?php echo $entry['id']; ?>)" title="<?php html(LANG_COMMENT_SHOW_PARENT); ?>">&uarr;</a>
                                    <?php } ?>
                                    <a href="#down" class="btn btn-sm border-0 d-none scroll-down" onclick="return icms.comments.down(this)" title="<?php echo html(LANG_COMMENT_SHOW_CHILD); ?>">&darr;</a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </h6>
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?>
<?php $this->addJS($this->getTplFilePath('js/comments.js', false)); ?>
<?php ob_start(); ?>
<div class="users_likers"></div>
<script>
    $('.icon').each(function(e) {
        $(this).mouseover(function() {
            var a = $(this).next('.list_likers');
            $(a).addClass('on');
            $(a).mouseleave(function(e) {
                $(this).removeClass('on');
            });
        });

        $(this).mouseleave(function(e) {
            var target = e.target || e.srcElement; // Иконка
            var e_target = e.relatedTarget; // Куда ушла мышь
            var a = $(this).next('.list_likers');
            var mouse = document.elementFromPoint(e.clientX, e.clientY);
            var value = e_target.attributes[0].nodeValue;
            if ($(a)[0] == mouse || value == 'col-sm-12' || value == 'row') { // 'col-sm-12 и row' внутренний код блока .list
                return;
            } else {
                $(a).removeClass('on');
            }
        });
    });

</script>
<?php $this->addBottom(ob_get_clean()); ?>