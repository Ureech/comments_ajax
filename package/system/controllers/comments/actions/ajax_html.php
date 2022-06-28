<?php

class actionCommentsAjaxHtml extends cmsAction
{

    public function run()
    {

       $number = $this->request->get('number',0);
        $target_subject = $this->request->get('subject');
        $target_controller = $this->request->get('controller');
        $target_id = $this->request->get('id');
        $target_user_id = $this->request->get('user_id');

        $html = $this->getHtml($number, $target_subject, $target_controller, $target_id,$target_user_id);

        return $this->cms_template->renderJSON(array('html'=>$html,'num'=>$number));
    }

    public function ctypeOptions($item,$target_subject){

        $datas = $this->model->filterEqual('name',$target_subject)->
        getItem('content_types');
        $data = $datas[$item];
       
        return $data;
    }

    public function getHtml($number, $target_subject, $target_controller, $target_id,$target_user_id)
    {


        $is_rating_newcomm = $this->ctypeOptions('is_rating_newcomm',$target_subject);

        $is_moderator = $this->cms_user->is_admin || cmsCore::getModel('moderation')->userIsContentModerator($this->name, $this->cms_user->id);

        if ($is_moderator) {
            $this->model->disableApprovedFilter();
        }

        if ($this->checkRows()) return false;

        $newrows = $this->model->filterEqual('name', $target_subject)->get('content_types');  // Добавленно

        foreach ($newrows as $row) {
            $count_comm = $row['count_comm'];
            $is_rating_newcomm = $row['is_rating_newcomm'];
            $is_score_newcomm = $row['is_score_newcomm'];
            $is_likes_newcomm = $row['is_likes_newcomm'];
        }

        cmsEventsManager::hook('comments_list_filter', $this->model);

        $comments_count = $this->model->filterCommentTarget(
            $target_controller,
            $target_subject,
            $target_id
        )->filterEqual('is_deleted',NULL)->getCommentsCount();

        $this->model->limit($number,$count_comm);  // Добавленно      
        if ($is_rating_newcomm) {
            $this->model->joinCommentsRating($this->cms_user->id);
        }
        $comments = $this->model->getComments();

        $comments = cmsEventsManager::hook('comments_before_list', $comments);
        list($comments, $comments_count) = cmsEventsManager::hook('comments_before_list_this', [$comments, $comments_count, $this]);

        $is_tracking = $this->model->filterCommentTarget(
            $target_controller,
            $target_subject,
            $target_id
        )->getTracking($this->cms_user->id);

        $is_highlight_new = $this->request->hasInQuery('new_comments');

        if ($is_highlight_new && !$this->cms_user->is_logged) {
            $is_highlight_new = false;
        }

        $csrf_token_seed = implode('/', array($target_controller, $target_subject, $target_id));

        $rss_link = '';
        if ($this->isControllerEnabled('rss') && $this->model->isRssFeedEnable()) {
            $rss_link = href_to('rss', 'feed', 'comments') . '?' . http_build_query([
                'tc' => $target_controller,
                'ts' => $target_subject,
                'ti' => $target_id
            ]);
        }

        if (!$this->comments_title) {
            $this->comments_title = ($comments_count ? html_spellcount($comments_count, $this->labels->spellcount) : $this->labels->comments);
        }

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        $editor_params['options']['id'] = 'content';

        // Контекст использования
        $editor_params['options']['upload_params'] = [
            'target_controller' => 'comments',
            'target_subject' => $target_subject
        ];

        $rating_log = $this->model->filterEqual('target_subject', $target_subject)->filterEqual('id', $target_id)->filterEqual('user_id', $this->cms_user->id)->getItem('comments_rating_log');
        $ip  = $this->cms_user->getIp();
        $rating_log_ip = $this->model->filterEqual('target_subject', $target_subject)->filterEqual('id', $target_id)->filterEqual('ip', $ip)->getItem('comments_rating_log');

        $html = $this->cms_template->renderInternal($this, 'list_my', [
            'user'              => $this->cms_user,
            'editor_params'     => $editor_params,
            'target_controller' => $target_controller,
            'target_subject'    => $target_subject,
            'target_id'         => $target_id,
            'target_user_id'    => $target_user_id,
            'is_karma_allowed'  => $this->cms_user->is_logged && !cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma),
            'is_guests_allowed' => !empty($this->options['is_guests']),
            'can_add'           => cmsUser::isAllowed('comments', 'add') || (!$this->cms_user->is_logged && !empty($this->options['is_guests'])),
            'is_tracking'       => $is_tracking,
            'is_highlight_new'  => $is_highlight_new,
            'comments'          => $comments,
            'csrf_token_seed'   => $csrf_token_seed,
            'rss_link'          => $rss_link,
            'guest_name'        => cmsUser::getCookie('comments_guest_name', 'string', function ($cookie) {
                return trim(strip_tags($cookie));
            }),
            'guest_email'       => cmsUser::getCookie('comments_guest_email', 'string', function ($cookie) {
                return trim(strip_tags($cookie));
            }),
            'count_comm'        => $count_comm,
            'is_rating_newcomm' =>  $is_rating_newcomm?$is_rating_newcomm:false,
            'is_score_newcomm' => $is_score_newcomm,
            'is_likes_newcomm' => $is_likes_newcomm?$is_likes_newcomm:false,
            'rating_log'       => $rating_log,
            'rating_log_ip'    => $rating_log_ip,
            'number_page'      => $count_comm,
            'comments_count'   => $comments_count,
            'a_href'            => '<a href="javascript:;" class="btn btn-info other_list" onclick="icms.newcomm.otherList('.$count_comm.','.$target_id.','.$target_user_id.')" >Ещё</a>',
            'is_can_rate'       => cmsUser::isAllowed('comments', 'rate')
        ]);

        return $html;
    }
}
