
<?php if (!$entry['is_deleted'] && $is_rating_newcomm) { ?>
            <div class="d-flex align-items-start flex-column mr-2 mr-lg-3 icms-comment-rating <?php echo $no_approved_class; ?>">
                <div class="d-flex align-items-center flex-column w-100">
                    <?php if ($is_can_rate && ($entry['user_id'] != $user->id) && empty($entry['is_rated'])) { ?>
                        <a href="#rate-up" class="icms-comment-rating_btn text-success rate-up" title="<?php echo html(LANG_COMMENT_RATE_UP); ?>" data-id="<?php echo $entry['id']; ?>">
                            <?php html_svg_icon('solid', 'caret-square-up'); ?>
                        </a>
                    <?php } else { ?>
                        <span class="rate-disabled">
                            <?php html_svg_icon('solid', 'caret-square-up'); ?>
                        </span>
                    <?php } ?>
                    <span class="value <?php echo html_signed_class($entry['rating']); ?>">
                        <?php echo $entry['rating'] ? html_signed_num($entry['rating']) : '0'; ?>
                    </span>
                    <?php if ($is_can_rate && ($entry['user_id'] != $user->id) && empty($entry['is_rated'])) { ?>
                        <a href="#rate-down" class="icms-comment-rating_btn rate-down text-danger" title="<?php echo html(LANG_COMMENT_RATE_DOWN); ?>" data-id="<?php echo $entry['id']; ?>">
                            <?php html_svg_icon('solid', 'caret-square-down'); ?>
                        </a>
                    <?php } else { ?>
                        <span class="rate-disabled">
                            <?php html_svg_icon('solid', 'caret-square-down'); ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>