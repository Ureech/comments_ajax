<?php

class actionCommentsDeleteNewcomm extends cmsAction
{

    public function run()
    {

        $error = false;
        $template = $this->cms_template->name;


        $is_score_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_score_newcomm'"
        );
        if ($this->model->db->numRows($is_score_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE  `cms_content_types` DROP COLUMN `is_score_newcomm`");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при удаления поля "is_score_newcomm" из таблице "cms_content_types"', 'error');
            }
        }
        $is_likes_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_likes_newcomm'"
        );
        if ($this->model->db->numRows($is_likes_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE  `cms_content_types` DROP COLUMN `is_likes_newcomm`");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при удаления поля "is_likes_newcomm" из таблице "cms_content_types"', 'error');
            }
        }
        $is_rating_newcomm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'is_rating_newcomm'"
        );
        if ($this->model->db->numRows($is_rating_newcomm)) {
            $do = $this->model->db->query("ALTER TABLE  `cms_content_types` DROP COLUMN `is_rating_newcomm`");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при удаления поля "is_rating_newcomm" из таблице "cms_content_types"', 'error');
            }
        }
        $count_comm = $this->model->db->query(
            "SHOW COLUMNS FROM `cms_content_types` LIKE 'count_comm'"
        );
        if ($this->model->db->numRows($count_comm)) {
            $do = $this->model->db->query("ALTER TABLE  `cms_content_types` DROP COLUMN `count_comm`");
            if (!$do) {
                $error = cmsUser::addSessionMessage('Ошибка при удаления поля "count_comm" из таблице "cms_content_types"', 'error');
            }
        }


        if (!$this->model->db->query(
            "DROP TABLE IF EXISTS cms_comments_ctype_rating,cms_comments_rating_log,cms_comments_likes_log"
        )) {
            $error = cmsUser::addSessionMessage('Ошибка при удалении таблиц', 'error');
        }

        $dirs = array(
            PATH . '/templates/' . $template . '/controllers/comments/css',
            PATH . '/templates/' . $template . '/controllers/comments/js',
            PATH . '/templates/' . $template . '/controllers/comments/images',
        );

        $files = array();


        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                if (!$this->rrmdir($dir)) {
                    $error = cmsUser::addSessionMessage('Ошибка при удалении дирректории : ' . $dir, 'error');
                }
            }
        }

        $files = [
            PATH . '/system/controllers/' . $this->name . '/hooks/ctype_news_before_item.php',

            PATH . '/system/controllers/' . $this->name . '/custom.php',
            PATH . '/system/controllers/' . $this->name . '/actions/ajax_html.php',
            PATH . '/system/controllers/' . $this->name . '/actions/like.php',
            PATH . '/system/controllers/' . $this->name . '/actions/score.php',

            PATH . '/system/controllers/admin/hooks/ctype_basic_form.php',
            PATH . '/system/controllers/admin/hooks/ctype_before_add.php',


            PATH . '/templates/' . $template . '/controllers/comments/comment_my.tpl.php',
            PATH . '/templates/' . $template . '/controllers/comments/comments_rating.tpl.php',
            PATH . '/templates/' . $template . '/controllers/comments/list_likers.tpl.php',
            PATH . '/templates/' . $template . '/controllers/comments/list_my.tpl.php',
            PATH . '/templates/' . $template . '/controllers/comments/score_block.tpl.php',
            PATH . '/templates/' . $template . '/controllers/comments/tab_list_my.tpl.php',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (!unlink($file)) {
                    $error = cmsUser::addSessionMessage('Ошибка при удалении файла : ' . $file, 'error');
                }
            }
        }

        if ($error) {
            return $error;
        } else {
            unlink(PATH.'/system/controllers/' . $this->name . '/actions/delete_newcomm.php');
         if($this->redirect('/admin/controllers/events_update')){
            return $this->redirectBack();
         }
        }
        
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        rrmdir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }

            reset($objects);
            return rmdir($dir);
        }
    }
}
