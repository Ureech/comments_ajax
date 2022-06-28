<?php
if ($usrs) { ?>
    <div class=" container-fluid">
        <div class="row overflow">
            <?php foreach ($usrs as $usr) { ?>
                <?php if($usr['id']){ ?>
                <div class="col-sm-3 p-2">
                    <a href="/users/<?php echo $usr['id']  ?>"> <?php echo html_avatar_image($usr['avatar'], 'small', $usr['nickname']); ?>
                        <p><?php echo $usr['nickname']; ?></p>
                    </a>
                </div>
            <?php } } ?>          
        </div>    
    </div>
    <span class="close">Закрыть</span>
<?php } ?>
