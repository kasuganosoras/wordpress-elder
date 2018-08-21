<?php
/**
 * @package Hello_Haha
 * @version 1.0
 */
/*
Plugin Name: 你好，蛤蛤！
Plugin URI: https://blog.kasuganosora.cn/
Description: 这不是一个普通的插件，它象征着一代人的膜蛤精神，以及大家对长者的热情，它会在前台或者后台每个页面显示一句来自“怒斥香港记者”的长者经典语录，不但能够让访客变得Excited，还能让你的博客变得更快。
Author: KasuganoSora
Version: 1.0
Author URI: https://blog.kasuganosora.cn/
*/

$PLUGIN_DIR =  plugins_url() . '/' . dirname(plugin_basename(__FILE__));

$lyrics = "江主席，你觉得董先生连任好不好啊？
好啊。
中央也支持他吗？
当然啊！
现在那么早你们就说支持董先生，会不会给人一种感觉，就是内定，钦点董先生？
没有任何意思。还是按照香港基本法，选举法，去产生......
刚才你问我，我可以回答你说“ 无可奉告”，你们也不高兴，那怎么办？
那董先生......
我的意思不是我钦点他当下任，你问我支持不支持，我是支持的，我就明确的告诉你。
你们啊，我感觉你们新闻界还要学习，你们非常熟悉西方的这一套媒体。
你们毕竟TOO YOUNG，你明白我的意思吗？
我告诉你们，我是身经百战了，见得多啊。
西方的哪一个国家我没有去过？
你们要知道美国的华莱士，比你们不知要高到哪里去了，哎，我也跟他谈笑风声。
媒体啊，还是要提高自己的知识水平，晓得不晓得啊？
唉，我也替你们着急啊，真的。
你们有一个好，全世界跑到什么地方，比其他的西方记者跑得还快，
但是问来问去的问题啊，都TOO SIMPLE，SOMETIMES NAIVE。
懂了没有？懂不懂得？
我很抱歉，我今天是作为一个长者跟你们讲，
我不是一个新闻工作者，但是我见得太多了，我有这个必要告诉你们一点人生的经验。
刚才我很想，我每次碰到你们......
中国有句话，叫“ 闷声大发财”。
我什么话也不说，这是最好的。
但是我想，我见到你们这样热情，一句话不说也不好。
所以刚才你一定要，在宣传上将来如果报道上有偏差，你们要负责，
我没有说要钦定，没有任何这个意思。
但是你们一定要问我，董先生你支持不支持。
我们不支持他，他现在当特首，我们怎么能不支持特首？
但是如果说连任呢？
连任要按照香港法律，对不对？
要......要要按照香港的......当然我们的决定权也是很重要的。
香港特别行政区属于中华人民共和国的中央政府！啊！
到那个时候我们会表态的。明白这意思吗？
你们呀，不要想喜欢弄大新闻，说现在已经钦定了，就把我批判一番，你们呐，NAIVE！
I AM ANGRY！你们这样是不行的！";

function hello_haha_get_lyric() {
	
	global $lyrics;
	
	$lrcs = get_option('plugin_haha_text') == "" ? $lyrics : get_option('plugin_haha_text');
	$lrcs = explode( "\n", $lrcs);
	return wptexturize( $lrcs[ mt_rand( 0, count( $lrcs ) - 1 ) ] );
}

function hello_haha() {
	$rand = hello_haha_get_lyric();
	echo "<p id='haha'>{$rand}</p>";
}

function hello_haha_front() {
	$rand = hello_haha_get_lyric();
	$rand = trim(str_replace("\n", "", $rand));
	echo "
	<script type='text/javascript'>
		try {
			var hahas = document.getElementsByClassName('haha');
			for(var i=0;i < hahas.length;i++) {
				hahas[i].innerHTML = '{$rand}';
			}
			//document.getElementById('haha').innerHTML = '{$rand}';
		} catch(Error) {
			// 无可奉告
		}
	</script>";
}

function haha_css() {
	
	$x = is_rtl() ? 'left' : 'right';
	$fontsize = get_option('plugin_haha_size') == "" ? '12' : get_option('plugin_haha_size');
	
	echo "
	<style type='text/css'>
	#haha {
		position: absolute;
		top: 16px;
		right: 16px;
		font-size: {$fontsize}px;
	}
	</style>
	";
}

add_action('admin_notices', 'hello_haha');
if(get_option('plugin_haha_front') == 'true') {
	add_action('wp_footer', 'hello_haha_front');
}
add_action('admin_head', 'haha_css');
add_action('admin_menu', 'plugin_haha');

function plugin_haha(){
    add_options_page('你好，蛤蛤！', '你好，蛤蛤', 'manage_options', 'plugin-haha','plugin_haha_option_page');
}

function haha_add_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
      $links[] = '<a href="options-general.php?page=plugin-haha">' . __('Settings') . '</a>';
    }
    return $links;
}
add_filter('plugin_action_links', 'haha_add_settings_link', 10, 2);

function plugin_haha_option_page(){
	
	global $lyrics;
	
	$updated = "";
    if(!current_user_can('manage_options')) {
		wp_die('你们呀，不要想喜欢弄大新闻，说现在已经钦定了，就把我批判一番。');
	}
    if(isset($_POST['size']) && isset($_POST['text'])){
        update_option('plugin_haha_size', $_POST['size']);
        update_option('plugin_haha_text',$_POST['text']);
        update_option('plugin_haha_front',$_POST['front']);
        $updated = '<p class="text-success">我什么话也不说，这是坠吼的。</p>';
    } ?>
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <div class="wrap">
		<div class="row">
			<div class="col-sm-8">
				<form action="options-general.php?page=plugin-haha" method="post">
					<div class="box">
						<div class="box-header">
							<div class="box-title">
								<h3>你好，蛤蛤</h3>
								<p>这不是一个普通的插件，它象征着一代人的膜蛤精神，以及大家对长者的热情，它会在前台或者后台每个页面显示一句来自“怒斥香港记者”的长者经典语录，不但能够让访客变得Excited，还能让你的博客变得更快。</p>
								<p>真正的粉丝，即使是一个简单的 WordPress 插件，也会感到自己的时间过得很快。+<span id="times">1s</span></p>
								<p>如果您钦定了 WP SuperCache，可能会导致前台的语录被缓存，很江硬，不会随机显示，建议关闭首页的缓存功能。</p>
								<hr>
							</div>
						</div>
						<div class="box-body">
							<?php wp_nonce_field('plugin-haha-options'); ?>
							<p>钦定的字体大小，单位 px</p>
							<p>
								<input type="text" maxlength="2" value="<?php echo get_option('plugin_haha_size') == "" ? '12' : get_option('plugin_haha_size'); ?>" name="size" class="form-control" />
							</p>
							<p>前台也显示蛤蛤语录好不好啊？在你需要显示蛤蛤语录的地方插入 <code>&lt;p class='haha'&gt;&lt;/p&gt;</code></p>
							<p>
								<select name="front" class="form-control">
									<option value="true"<?php echo get_option('plugin_haha_front') == "true" ? ' selected="selected"' : ""; ?>>吼啊</option>
									<option value="false"<?php echo get_option('plugin_haha_front') == "false" ? ' selected="selected"' : ""; ?>>不吼</option>
								</select>
							</p>
							<p>江来报道的语录列表，每行一条</p>
							<p>
								<textarea name="text" class="form-control" style="height: 512px;"><?php echo get_option('plugin_haha_text') == "" ? $lyrics : get_option('plugin_haha_text'); ?></textarea>
							</p>
						</div>
						<div class="box-footer text-right">
							<?php echo $updated; ?>
							<input name="update_options" value="中央已经决定了" title="你来当总书记" type="submit" class="btn btn-danger" />
						</div>
					</div>
				</form>
				<script>
					var timess = 2;
					setInterval(function() {
						times.innerHTML = timess + "s";
						timess++;
					}, 500);
				</script>
			</div>
			<div class="col-sm-4">
				<div class="text-center" style="margin-top: 48px;">
					<img style="border-radius: 50%;" src="https://i.natfrp.org/6d8ba085520d8db2ca186dbe9536cb99.jpg" />
					<br><br>
					<p>你看我泽个插件写的这么辛苦，资瓷一下吼不吼啊~</p>
					<p><a href="https://github.com/KasuganoSoras/wordpress-elder" class="text-danger" target="_blank">https://github.com/KasuganoSoras/wordpress-elder</a> 求Star，谢谢！</p>
					<p>Hello_Haha v1.0 by KasuganoSora</p>
				</div>
			</div>
		</div>
    </div><?php        
}
