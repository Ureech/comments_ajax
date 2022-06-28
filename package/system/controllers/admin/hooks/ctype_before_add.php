<?php

class onAdminCtypeBeforeAdd extends cmsAction
{

    public function run($ctype)
    {

        $ctype['is_rating_newcomm'] = 0;
        $ctype['is_score_newcomm'] = 0;
        $ctype['is_likes_newcomm'] = 0;

        return $ctype;
    }
}
