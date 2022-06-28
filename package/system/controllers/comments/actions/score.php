<?php

/**
 * @property \modelComments $model
 */
class actionCommentsScore extends cmsAction
{

    public function run()
    {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $user_id   = $this->request->get('user_id', 0);
        $ctype = $this->request->get('ctype');
        $units    = $this->request->get('units', 0);
        $ip        = $this->cms_user->getIp();
        $id        = $this->request->get('id', 0);


        $this->model->filterEqual('target_subject', $ctype);
        $this->model->filterEqual('id', $id);
        $this->model->filterEqual('user_id', $user_id);
        $user_log = $this->model->getItem('comments_rating_log');

        // $raiting = $this->controller->model->filterEqual('id', $id)->getItem('con_' . $ctype);

        $score   = $this->controller->model->filterEqual('ctype', $ctype)->filterEqual('id', $id)->getItem('comments_ctype_rating');

        if ($user_log && $user_log['ip'] == $ip) {
            return $this->cms_template->renderJSON([
                'error' => true,
            ]);
        }

        $this->model->filterEqual('ctype', $ctype)->filterEqual('id', $id)->insertOrUpdate('comments_ctype_rating', [
            'ctype' => $ctype,
            'id'    => $id,
            'total_value' => intval($units + $score['total_value']),
            'total_votes' => intval($score['total_votes'] + 1)
        ]);

        $this->model->insert('comments_rating_log', array(
            'id' => $id,
            'user_id' => $user_id,
            'target_subject' => $ctype,
            'ip'      => $ip
        ));


        return $this->cms_template->renderJSON([
            'error'          => false,
        ]);
    }
}
