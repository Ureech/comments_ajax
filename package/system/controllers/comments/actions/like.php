<?php

/**
 * @property \modelComments $model
 */
class actionCommentsLike extends cmsAction
{

    public function run()
    {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $user_id   = $this->request->get('user_id', 0);
        $user_name = $this->request->get('user_name', 0);
        $ip        = $this->cms_user->getIp();
        $id        = $this->request->get('id', 0);
        $all        = $this->request->get('all', 0);

        $comment = $this->model->getComment($this->request->get('id', 0));

        // Проверяем
        if (!$comment) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        if ($all) {

            $likes_users = $this->model->filterEqual('comment_id', $id)->get('comments_likes_log');
            $usr_model = cmsCore::getModel('users');
            foreach ($likes_users as $likers) {
                $user = $usr_model->getUser($likers['user_id']);
                $users[] = $user;
            }
            if ($users) {
                $html = $this->cms_template->render('list_likers', array('usrs' => $users));
                return $this->cms_template->renderJSON([$html]);
            }
        }

        $likes_log = $this->model->filterEqual('comment_id', $id)->getItem('comments_likes_log');
        $liked =  $likes_log && ($likes_log['user_id'] == $user_id) && ($likes_log['ip'] == $ip || $likes_log['author_name'] == $user_name);


        if ($liked) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        if (!$user_id && !$user_name) $author_name = 'goust';
        if (!$user_id && $user_name) $author_name  = $user_name;
        
        $this->model->insert('comments_likes_log', [
            'comment_id' => $this->request->get('id', 0),
            'user_id'    => $user_id,
            'author_name' => $author_name,
            'ip'  => $ip
        ]);

        


        return $this->cms_template->renderJSON([
            'error'          => false,
        ]);
    }
}
