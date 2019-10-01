<?php
/**
 * B站追番列表
 *
 * @version:2.0.0
 * @author AyagawaSeirin
 * https://github.com/AyagawaSeirin/BilibiliFollowPage
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;


$bilibiliUser = $this->fields->BilibiliUid;
$cacheTime = $this->fields->CacheTime;
$amout = $this->fields->Amout;
$hideMedia = $this->fields->HideMedia;

if ($bilibiliUser == "" || $bilibiliUser == null) {
    $bilibiliUser = '174471710';
}
if ($cacheTime == "" || $cacheTime == null) $cacheTime = 86400;
if ($amout == "" || $amout == null || $amout > 100) $amout = 100;

/**
 * 作为异步接口时的操作
 */
if ($_POST['post'] == '1') {
    $dirPath = __DIR__ . '/assets/cache/BilibiliFollow';
    $filePath = $dirPath . '/BilibiliFollow.json';
    //检测缓存目录是否存在，不存在则创建
    if (is_dir($dirPath) == false) {
        mkdir($dirPath, 0777, true);
        $update = true;
    }
    //检测缓存文件是否存在，不存在则创建
    if (file_exists($filePath) == false) {
        fopen($filePath, "w");
        $update = true;
    }
    //若初步检查就要更新文件，则直接返回更新程序返回的缓存内容
    if ($update == true) {
        echo updateDate($bilibiliUser, $cacheTime, $amout,$hideMedia);
        return;
    }

    //读取缓存文件
    $fileCache = fopen($filePath, "r");
    $contents = fread($fileCache, filesize($filePath));
    fclose($fileCache);
    $data = json_decode($contents, true);
    if (time() - $data['time'] > $cacheTime || $data['BilibiliUid'] != $bilibiliUser || $data['amout'] != $amout) {
        //缓存过期或B站UID更新或输出数量更新
        echo updateDate($bilibiliUser, $cacheTime, $amout,$hideMedia);
        return;
    }
    echo $contents;
    return;
}

/**
 * 更新缓存数据
 * @param $userID
 * @param $cacheTime
 * @param $amout
 * @return mixed
 */
function updateDate($userID, $cacheTime, $amout,$hideMedia)
{
    $dirPath = __DIR__ . '/assets/cache/BilibiliFollow';
    $filePath = $dirPath . '/BilibiliFollow.json';
    //执行更新程序的前提是缓存数据文件都已经存在了
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.bilibili.com/x/space/bangumi/follow/list?type=1&follow_status=0&pn=1&ps=" . $amout . "&ts=" . rand(9999999, 99999999) . "&vmid=" . $userID);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Origin: https://space.bilibili.com","Referer: https://space.bilibili.com/".$userID."/bangumi"));
    $output = curl_exec($ch);
    curl_close($ch);

    $hideMedia = explode(",",$hideMedia);
    $data = json_decode($output, true);

    $i = 0;
    //开始苦逼的缓存图片
    foreach ($data['data']['list'] as $value) {
        $imgUrl = $value['cover'];
        $imgPath = $dirPath.'/'.$value['media_id'].'.jpg';

        if (file_exists($imgPath) == false) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch,CURLOPT_URL,$imgUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Origin: https://space.bilibili.com","Referer: https://space.bilibili.com/".$userID."/bangumi"));
            $file_content = curl_exec($ch);
            curl_close($ch);
            $img_file = fopen($imgPath, 'w');
            fwrite($img_file, $file_content);
            fclose($img_file);
        }

        if(in_array($value['media_id'],$hideMedia)){
            $display = '0';
        } else {
            $display = '1';
        }
        $data['data']['list'][$i]['display'] = $display;
        $i++;
    }

    $data['time'] = time();
    $data['BilibiliUid'] = $userID;
    $data['amout'] = $amout;

    $data = json_encode($data);
    $file = fopen($filePath, "w");
    fwrite($file, $data);
    fclose($file);

    return $data;
}

?>

<?php $this->need('component/header.php'); ?>

<!-- aside -->
<?php $this->need('component/aside.php'); ?>
<!-- / aside -->

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
                                    .wrapper-lg {
                                        padding: 10px !important;
                                    }
                                    .seirin-bilibili-follow-panel {
                                        padding: 0 !important;
                                    }
                                    .seirin-bilibili-follow-img {
                                        width: 95%;
                                        max-width: 140px !important;
                                        margin: 0 !important;
                                        padding: 0 !important;

                                    }
                                    .seirin-bilibili-follow-title {
                                        font-weight: 400;
                                        line-height: 24px;
                                        color: #222;
                                        font-size: 22px;
                                        margin-bottom: 5px !important;
                                        margin-top: 2px !important;
                                    }
                                    .seirin-bilibili-follow-text {
                                        color: #222;
                                        margin-bottom: 10px !important;
                                    }

                                    .seirin-bilibili-follow-info {
                                        font-size: 12px;
                                        margin-bottom: 0 !important;
                                    }
                                    .seirin-bilibili-follow-content{
                                        padding-top:15px;
                                        padding-right: 20px;
                                        padding-left:0!important;
                                        padding-bottom:15px;
                                    }
                                    .seirin-bilibili-follow-panel-big{
                                        margin-bottom: 10px;
                                    }
                                    @media screen and (max-width:991px) {
                                        .seirin-bilibili-follow-content{
                                            padding-left:25px!important;
                                        }
                                        .seirin-bilibili-follow-img {
                                            display:none;
                                        }
                                        .seirin-bilibili-follow-content {
                                            padding-top:10px;
                                        }
                                    }
                                </style>
                                <!--
                                B站追番列表
                                @version:2.0.0
                                @author AyagawaSeirin
                                https://github.com/AyagawaSeirin/BilibiliFollowPage
                                https://qwq.best/dev/84.html
                                -->
                                <div class="seirin-bilibili-page">
                                    <nav class="loading-nav text-center m-t-lg m-b-lg">
                                        <p class="infinite-scroll-request"><i class="animate-spin fontello
                      fontello-refresh"></i><?php _me("正在加载，请稍后") ?></p>
                                    </nav>
                                    <nav class="error-nav hide text-center m-t-lg m-b-lg">
                                        <p class="infinite-scroll-request"><i class="glyphicon
                            glyphicon-refresh"></i>加载失败！请刷新再试~</p>
                                    </nav>
                                </div>
                                <script type="text/javascript">
                                    console.log("\n %c BilibiliFollowPage v2.0.0 %c by AyagawaSeirin | qwq.best ","color:#444;background:#eee;padding:5px 0;","color:#F8F8FF;background:#F4A7B9;padding:5px 0;");
                                    console.log("  BilibiliFollowPage : https://qwq.best/dev/84.html\n\n");
                                    var bilibiliItemTemple = '<div class="panel panel-default seirin-bilibili-follow-panel-big">'+
                                        '<div class="panel-body seirin-bilibili-follow-panel">'+
                                        '   <div class="row">'+
                                        '       <div class="col-md-3" style="padding-right:2px;">'+
                                        '           <img src="//<?=$_SERVER['SERVER_NAME']?>/usr/themes/handsome/assets/cache/BilibiliFollow/{media_id}.jpg" alt="{title}" class="seirin-bilibili-follow-img" class="lazy">'+
                                        '       </div>'+
                                        '       <a href="https://www.bilibili.com/bangumi/media/md{media_id}" target="_blank"><div class="col-md-9 seirin-bilibili-follow-content" style="padding-left:3;">'+
                                        '          <p class="seirin-bilibili-follow-title">{title}</p>'+
                                        '           <p class="seirin-bilibili-follow-text">{evaluate}</p>'+
                                        '           <p class="seirin-bilibili-follow-info">{season_type_name} | {areas_0_name} | {new_ep_index_show}</p>'+
                                        '       </div></a>'+
                                        '   </div>'+
                                        '</div>'+
                                        '</div>';

                                    var open = function(){
                                        var devContainer = $('.seirin-bilibili-page');
                                        var loadingContainer = devContainer.find(".loading-nav");
                                        var errorContainer = devContainer.find(".error-nav");
                                        $.ajax({
                                            url: "<?=$_SERVER['REQUEST_URI']?>",
                                            async: true,
                                            type: 'POST',
                                            data: 'post=1',
                                            dataType: 'json',
                                            success: function (data) {
                                                loadingContainer.addClass("hide");

                                                var list = data['data']['list'];
                                                for(var i in list){
                                                    var now = list[i];

                                                    if(now['display'] == '1') {
                                                        //匹配替换
                                                        var item = bilibiliItemTemple.replace("{title}", now['title']).replace("{title}", now['title'])
                                                            .replace("{media_id}", now['media_id'])
                                                            .replace("{media_id}", now['media_id'])
                                                            .replace("{evaluate}", now['evaluate'])
                                                            .replace("{season_type_name}", now['season_type_name'])
                                                            .replace("{areas_0_name}", now['areas'][0]['name'])
                                                            .replace("{new_ep_index_show}", now['new_ep']['index_show']);
                                                        devContainer.append(item);
                                                    }
                                                }
                                            },
                                            error:function () {
                                                loadingContainer.addClass("hide");
                                                errorContainer.removeClass("hide");
                                            }
                                        });
                                    };

                                    open();
                                </script>
                                <?php echo Content::postContent($this, $this->user->hasLogin()); ?>
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