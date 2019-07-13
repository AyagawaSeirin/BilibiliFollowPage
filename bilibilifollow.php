<?php
/**
 * B站追番列表
 *
 * @version:1.0.0
 * @author AyagawaSeirin
 * https://github.com/AyagawaSeirin/BilibiliFollowPage
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

?>

<?php $this->need('component/header.php'); ?>

<!-- aside -->
<?php $this->need('component/aside.php'); ?>
<!-- / aside -->

<?php
function getFollowData($userID, $cacheTime, $amout)
{
    $filePath = __DIR__ . '/assets/cache/BilibiliFollow.json';

    $fp = fopen($filePath, 'r');
    if ($fp) {
        $contents = fread($fp, filesize($filePath));
        fclose($fp);
        $data = json_decode($contents, true);
        if (time() - $data['time'] > $cahceTime || $data['BilibiliUid'] != $userID || $data['amout'] != $amout) {
            //缓存过期或B站UID更新或输出数量更新
            $data = updateDate($userID, $cacheTime, $amout);
        }
    } else {
        //缓存文件不存在
        $data = updateDate($userID, $cacheTime, $amout);
    }
    return $data;
}

function updateDate($userID, $cacheTime, $amout)
{
    $filePath = __DIR__ . '/assets/cache/BilibiliFollow.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.bilibili.com/x/space/bangumi/follow/list?type=1&follow_status=0&pn=1&ps=" . $amout . "&ts=" . rand(9999999, 99999999) . "&vmid=" . $userID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($output, true);
    $data['time'] = time();
    $data['BilibiliUid'] = $userID;
    $data['amout'] = $amout;

    $file = fopen($filePath, "w");
    fwrite($file, json_encode($data));
    fclose($file);

    echo "<!--本次访问更新了缓存文件-->";
    return $data;
}

?>
<!-- <div id="content" class="app-content"> -->
<a class="off-screen-toggle hide"></a>
<main class="app-content-body <?php Content::returnPageAnimateClass($this); ?>">
    <div class="hbox hbox-auto-xs hbox-auto-sm">
        <!--文章-->
        <div class="col center-part">
            <div class="bg-light lter b-b wrapper-md">
                <h1 class="m-n font-thin h3"><i class="iconfont icon-fork i-sm m-r-sm"></i><?php _me("追番列表") ?></h1>
                <div class="entry-meta text-muted  m-b-none small post-head-icon"><?php echo $this->fields->intro; ?></div>
            </div>
            <div class="wrapper-md" id="post-panel">
                <!--博客文章样式 begin with .blog-post-->
                <div id="postpage" class="blog-post">
                    <article class="panel">
                        <!--文章页面的头图-->
                        <?php echo Content::exportHeaderImg($this); ?>
                        <!--文章内容-->
                        <div id="post-content" class="wrapper-lg">
                            <div class="seirin-bilibili-follow-panel-all">
                                <style>
                                    .wrapper-lg{
                                        padding:10px!important;
                                    }
                                    .seirin-bilibili-follow-panel{
                                        padding:15px!important;
                                    }
                                    .seirin-bilibili-follow-img{
                                        width:95%;
                                        max-width: 140px!important;
                                        margin:0!important;
                                    }
                                    .seirin-bilibili-follow-title{
                                        font-weight: 400;
                                        line-height: 24px;
                                        color: #222;
                                        font-size: 22px;
                                        margin-bottom:5px!important;
                                        margin-top:2px!important;
                                    }
                                    .seirin-bilibili-follow-text{
                                        color: #222;
                                        margin-bottom:10px!important;
                                    }
                                    .seirin-bilibili-follow-info{
                                        font-size: 12px;
                                        margin-bottom:0!important;
                                    }
                                </style>
                                <?php
                                $bilibiliUser = $this->fields->BilibiliUid;
                                $cacheTime = $this->fields->CacheTime;
                                $amout = $this->fields->Amout;

                                if ($bilibiliUser == "" || $bilibiliUser == null) {
                                    $bilibiliUser = '174471710';
                                    echo "<script>console.log('页面参数BilibiliUid检测为空或错误，已更换为174471710用户(本页面作者)追番列表')</script>";
                                }
                                if ($cacheTime == "" || $cacheTime == null) $cacheTime = 86400;
                                if ($amout == "" || $amout == null || $amout > 100) $amout = 100;
                                $data = getFollowData($bilibiliUser, $cacheTime, $amout);
                                foreach ($data['data']['list'] as $value) {
                                    ?>
                                <div class="panel panel-default">
                                    <div class="panel-body seirin-bilibili-follow-panel">
                                        <div class="row">
                                            <div class="col-md-3" style="padding-right:2px;">
                                                <img src="<?=$value['cover']?>" alt="<?=$value['title']?>" class="seirin-bilibili-follow-img">
                                            </div>
                                            <div class="col-md-9" style="padding-left:3;">
                                                <a href="https://www.bilibili.com/bangumi/media/md<?=$value['media_id']?>" target="_blank"><p class="seirin-bilibili-follow-title"><?=$value['title']?></p></a>
                                                <p class="seirin-bilibili-follow-text"><?=$value['evaluate']?></p>
                                                <p class="seirin-bilibili-follow-info"><?=$value['season_type_name']?> | <?=$value['areas'][0]['name']?> | <?=$value['new_ep']['index_show']?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <?php
                                }
                                ?>


                                <?php echo Content::postContent($this, $this->user->hasLogin()); ?>
                                <!--<small class="text-muted letterspacing github_tips"></small>-->
                            </div>
                        </div>
                    </article>
                </div>
                <!--评论-->
                <?php $this->need('component/comments.php') ?>
            </div>
        </div>
        <!--文章右侧边栏开始-->
        <?php $this->need('component/sidebar.php'); ?>
        <!--文章右侧边栏结束-->
    </div>
</main>


<!-- footer -->
<?php $this->need('component/footer.php'); ?>
<!-- / footer -->

