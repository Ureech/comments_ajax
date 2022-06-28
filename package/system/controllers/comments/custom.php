<?php

class comments_custom extends comments
{
    const number_page = 3;

    public function __construct($request)
    {

        $this->name = str_replace('_custom', '', strtolower(get_called_class()));

        parent::__construct($request);
    }



    public function getWidget()
    {
        $number_page = $this->model->filterEqual('name',$this->target_subject)->getItem('content_types');
        $number_page =  $number_page['count_comm'];
        $comment_systems = cmsEventsManager::hookAll('comment_systems', $this, array());

        $rss_link = '';
        if ($this->isControllerEnabled('rss') && $this->model->isRssFeedEnable()) {
            $rss_link = href_to('rss', 'feed', 'comments') . '?' . http_build_query([
                'tc' => $this->target_controller,
                'ts' => $this->target_subject,
                'ti' => $this->target_id
            ]);
        }
        $comments_count = $this->model->filterCommentTarget(
            $this->target_controller,
            $this->target_subject,
            $this->target_id
        )->filterEqual('is_deleted',NULL)->getCommentsCount();

        if (!$this->comments_title) {
            $this->comments_title = ($comments_count ? html_spellcount($comments_count, $this->labels->spellcount) : $this->labels->comments);
        }

        $is_highlight_new = $this->request->hasInQuery('new_comments');

        if ($is_highlight_new && !$this->cms_user->is_logged) {
            $is_highlight_new = false;
        }

        $array = [
            'name'  => 'icms',
            'icon'  => $rss_link ? ['href' => $rss_link, 'icon' => 'rss', 'title' => 'RSS', 'class' => 'inline_rss_icon'] : [],
            'title' => $this->comments_title,

        ];

        if (empty($this->options['disable_icms_comments']) || !$comment_systems) {
            array_unshift($comment_systems, $array);
        }

        return $this->cms_template->renderInternal($this, 'tab_list_my', array(
            'comment_systems' => $comment_systems,
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $this->target_id,
            'target_user_id'    => $this->target_user_id,
            'is_highlight_new'  => $is_highlight_new,
            'number_page'       => $number_page,
            'comments_count'   => $comments_count,
           
        ));
    }

    public function getNativeComments()
    {
        return array();
    }

    public function checkRows()
    {

        $error = false;

        $is_count_comm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'count_comm'"
        );
        if (!$this->model->db->numRows($is_count_comm)) {
            $do = $this->model->db->query("ALTER TABLE `cms_content_types` ADD `count_comm` INT(5) NOT NULL DEFAULT '10'");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при создания поля "count_comm" в таблице "cms_content_types"', 'error');
            }
        }

        $is_rating_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_rating_newcomm'"
        );
        if (!$this->model->db->numRows($is_rating_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE `cms_content_types` ADD `is_rating_newcomm` TINYINT(1) NOT NULL DEFAULT '0'");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при создания поля "is_rating_newcomm" в таблице "cms_content_types"', 'error');
            }
        }

        $is_score_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_score_newcomm'"
        );
        if (!$this->model->db->numRows($is_score_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE `cms_content_types` ADD `is_score_newcomm` TINYINT(1) NOT NULL DEFAULT '0'");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при создания поля "is_score_newcomm" в таблице "cms_content_types"', 'error');
            }
        }

        $is_likes_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_likes_newcomm'"
        );
        if (!$this->model->db->numRows($is_score_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE `cms_content_types` ADD `is_likes_newcomm` TINYINT(1) NOT NULL DEFAULT '0'");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при создания поля "is_likes_newcomm" в таблице "cms_content_types"', 'error');
            }
        }

        return $error;
    }

    public function likesPerm($entry)
    {

        $ip        = $this->cms_user->getIp();
        $this->model->resetFilters();
        $likes_log = $this->model->filterEqual('comment_id', $entry['id'])->filterEqual('user_id', $this->cms_user->id)->getItem('comments_likes_log');
        if ($this->cms_user->id == $entry['user_id'] || ($entry['author_name'] == $likes_log['author_name'] && $likes_log['ip'] == $ip)) return false;
        if ($likes_log) return false;
        if (!$this->cms_user->id) {
            if ($likes_log['author_name'] == 'goust' && $likes_log['ip'] == $ip) return false;
        }

        return true;
    }

    public function Liked($entry)
    {

        $this->model->resetFilters();
        $likes_log = $this->model->filterEqual('comment_id', $entry['id'])->getItem('comments_likes_log');
        if ($likes_log) return true;
        return false;
    }
}
