<?php

//Uses the Textpattern plugin template tool from:
//http://textpattern.googlecode.com/svn/development/4.x-plugin-template/

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ('abc' is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'atb_twitter';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 0;

$plugin['version'] = '0.9.4';
$plugin['author'] = 'Adam T. Bradley';
$plugin['author_uri'] = 'http://twitter.com/daydreamlab';
$plugin['description'] = 'Display Twitter/StatusNet feeds on your website.';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
# $plugin['order'] = 5;

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
# $plugin['type'] = 0;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use. 
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

# $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;

if (!defined('txpinterface'))
	@include_once('zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---
    
h1. @atb_twitter@ Help

h2. Display Twitter feeds on your website

h3. Contents

* "Overview":#atb_tw_overview
* "Tags":#atb_tw_tags
* "Example":#atb_tw_example
* "To-Do":#atb_tw_todo

h3(#atb_tw_overview). Overview

@atb_twitter@ pulls user update feeds from Twitter or from StatusNet-based microblogging sites like identi.ca and displays them using a Textpattern form.

h3(#atb_tw_tags). Tags

h4. @txp:atb_twitter@

@txp:atb_twitter@ is the primary tag for this plugin, and acts as either a container or single tag. It takes the following attributes:

* *user:* _(Required)_ The screen name of the Twitter account to read from.
* *count:* _(Optional; defaults to '5')_ The number of recent statuses to pull.
* *cache:* _(Optional; defaults to '15')_ The maximum time, in minutes, to continue using cached results.
* *form:* _(Optional)_ The form to use to display statuses; required if @atb_twitter@ is not used as a container tag.
* *api_base:* _(Optional)_ The root URL of the microblogging site's API. Defaults to 'twitter.com'
* *site_base:* _(Optional)_ The base URL of the site to use when generating links. By default, @atb_twitter@ will attempt to guess this from the @api_base@; you shouldn't need to set this normally.
* *update_lastmod:* _(Optional)_ If set to "1" (the default), atb_twitter will update Textpattern's last update time with the time of the most recent Tweet (analogous to setting "New comment means site updated?" to yes in your site preferences).

h4. Tags for twitter forms:

Only a few of these tags take any attributes:

* *atb_tw_text* returns the tweet's text. If the @linkify@ attribute is left at 1 (its default), URLs, hashtags, and @replies will be converted to links and given CSS classes: all links are given the class "atb_status_link"; converted URLs additionally take the class "atb_status_weblink"; @replies get "atb_status_atreply" as well as a customized class based on the username mentioned, like "atb_status_atreply_to_[username]"; hashtags get "atb_status_hashtag" and another class based on the hashtag itself, like "atb_hashtag_[tag]". This tag takes the following attributes, all optional:
** *linkify:* With @linkify@ set to 1 (the default), URLs, at-replies and hashtags are converted to links; with @linkify@ set to 0, the text will be left alone. The remaining attributes will be ignored if @linkify@ is set to 0.
** *link_wrap:* The name of a tag to wrap around URLS that have been converted to links.
** *link_wrapclass:* The class of the URL wraptags.
** *atreply_wrap:* The name of a tag to wrap around @reply links.
** *atreply_wrapclass:* The class for @reply wraptags.
** *hashtag_wrap:* The name of a tag to wrap around hashtag links.
** *hashtag_wrapclass:* The class for hashtag wraptags.
* *atb_tw_created_at* returns the date of the tweet. It takes two attributes: @format@, which may be any value accepted by "txp:posted":http://textpattern.net/wiki/index.php?title=posted and defaults to the "Archive date format" as set in Textpattern's preferences; and @tz@, which determines if the time used is the local time (as determined by Textpattern's timezone--this is the default setting), GMT (set @tz@ to "1"), or the time zone Twitter reports for the user (set @tz@ to "2").
* *atb_tw_user_created_at* returns the date the Twitter account being read was created. It takes the same attributes as @atb_tw_created_at@.

There are three conditional tags:

* *atb_tw_if_is_reply*
* *atb_tw_if_first_tweet*
* *atb_tw_if_last_tweet*

The remaining tags accept no attributes and simply return text. These four return information about the current tweet/status:

* *atb_tw_id:* Returns the current status's id.
* *atb_tw_in_reply_to_status_id*
* *atb_tw_in_reply_to_user_id*
* *atb_tw_in_reply_to_screen_name*
* *atb_tw_source:* names and links to the program used to post the current status.

These tags return information about the user:

* *atb_tw_user_description:* The user's profile
        description
* *atb_tw_user_favourites_count*
* *atb_tw_user_followers_count*
* *atb_tw_user_friends_count*
* *atb_tw_user_id:* a numeric user ID.
* *atb_tw_user_lang*
* *atb_tw_user_location*
* *atb_tw_user_name:* The user's name as entered in his/her profile.
* *atb_tw_user_profile_image_url*
* *atb_tw_user_screen_name*
* *atb_tw_user_statuses_count*
* *atb_tw_user_url:* The website URL provided in the user's profile.

h3(#atb_tw_example). Example

This is just a basic form that displays my most recent 20 statuses:

bc.. <txp:atb_twitter count="20" user="daydreamlab">

<txp:atb_tw_if_first_tweet>
<h2><a href="http://twitter.com/<txp:atb_tw_user_screen_name />">@daydreamlab on Twitter</a></h2>
<ul>
</txp:atb_tw_if_first_tweet>

<li>
<span class="tweetdate"><txp:atb_tw_created_at />:</span> 
<br /><txp:atb_tw_text />

<txp:atb_tw_if_is_reply>
<br />
<a class="inreply" href="http://twitter.com/<txp:atb_tw_in_reply_to_screen_name />/status/<txp:atb_tw_in_reply_to_status_id />">
in reply to <txp:atb_tw_in_reply_to_screen_name />
</a>
</txp:atb_tw_if_is_reply>
</li>

<txp:atb_tw_if_last_tweet>
</ul>
</txp:atb_tw_if_last_tweet>

</txp:atb_twitter>

h3. To Do

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

/**
 * Textpattern plugin to put Twitter feeds on the site.
 *
 * @author Adam Bradley <hisself@adambradley.net>
 */

/**
 * Main atb_twitter txp:tag.
 */
function atb_twitter($atts, $thing) {
    global $atb_thistweet;
    
    extract(lAtts(array(
                        'user' => '',
                        'count' => 5,
                        'cache' => 15,
                        'form' => '',
                        'api_base' => '',
                        'site_base' => 'twitter.com',
                        'update_lastmod' => 1
                       ),
                  $atts));
    
    //Generate a hash of the attributes to use as a cache file ID.
    $hash = md5(implode($atts + array($thing)));
    
    //If there's cached output for this request, just return it.
    if ( $outp = atb_get_tweet_cache($hash, $cache * 60) ) { return $outp; }
    
    $template = $thing ? $thing : fetch_form($form);
    
    if (!$template) {
        //Nothing to do.
        return '';
    }
    
    if ( !$site_base ) {
        if ( $api_base === '' ) {
            $api_base = 'api.twitter.com/1';
            $site_base = 'twitter.com';
        } else {
            $api_base = trim($api_base, '/ ');
            $site_base .= preg_replace('/(\/api$)/', '', $api_base);
        }
    } elseif ( 'twitter.com' === $site_base ) {
        $api_base = 'api.twitter.com/1';
    } elseif ( !$api_base ) {
        $api_base = $site_base.'/api';
    }
    
    $tweets = atb_get_tweets($user, $count, $api_base);
    if (!$tweets) {
        //Probable fail whale. Try using an old, outdated cache file
        //(so at least we'll have *some* content).
        $outp = atb_get_tweet_cache($hash, 0);
        return $outp ? $outp : '';
    }
    
    //Update Textpattern's last-modified date.
    if ( $update_lastmod ) {
        $currentmod = strtotime(get_pref('lastmod', false));
        $twitmod = $tweets->xpath('//status/created_at');
        $twitmod = (string)$twitmod[0];
        $twitmod = strtotime($twitmod);
        
        if ( $currentmod && $currentmod < $twitmod ) {
            set_pref('lastmod', strftime('%F %H:%M:%S', $twitmod));
        }
    }
    
    $tweets = $tweets->xpath('//status');
    $outp = '';
    
    foreach ( $tweets as $twindex => $atb_thistweet)
    {
        $atb_thistweet->addAttribute('index', $twindex + 1);
        $atb_thistweet->addAttribute('total', count($tweets));
        $atb_thistweet->addAttribute('site_base', $site_base);
        
        $outp .= parse($template);
    }
    
    atb_put_tweet_cache($hash, $outp);
    
    return $outp;
}


/* Txp form tags
  ----------------------------------------------------------------------------*/
function atb_tw_text($atts) {
    global $atb_thistweet;
    
    $atts = lAtts(array('linkify' => 1,
                        'link_wrap' => '',
                        'link_wrapclass' => '',
                        'atreply_wrap' => '',
                        'atreply_wrapclass' => '',
                        'hashtag_wrap' => '',
                        'hashtag_wrapclass' => ''),
              $atts);
    
    extract($atts);
    
    if ( $linkify )  { return atb_linkify_tweet($atb_thistweet->text,
                                                (string)$atb_thistweet['site_base'],
                                                $atts); }
    else { return $atb_thistweet->text; }
}

function atb_tw_if_first_tweet($atts, $thing) {
    global $atb_thistweet; 
    return parse(EvalElse($thing, $atb_thistweet['index'] == 1));
}

function atb_tw_if_last_tweet($atts, $thing) {
    global $atb_thistweet; 
    return parse(EvalElse($thing, (int)$atb_thistweet['index'] === (int)$atb_thistweet['total']));
}

function atb_tw_if_is_reply($atts, $thing) {
    global $atb_thistweet;
    
    return parse(EvalElse($thing, $atb_thistweet->in_reply_to_status_id != ''));
}

function atb_tw_created_at($atts) {
    global $atb_thistweet;
    extract(lAtts(array('format' => '',
                        'tz' => '0'),
              $atts));
    
    $time = strtotime($atb_thistweet->created_at);
    
    if ( !$format ) { global $prefs; $format = $prefs['archive_dateformat']; }
    
    if ( $tz === 2 ) {
        $tz = 1;
        $time += (int)$atb_thistweet->user->utc_offset;
    }
    return safe_strftime($format, $time, (int)$gmt);
}

function atb_tw_id($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_', '', __FUNCTION__);
    return $atb_thistweet->$node;
}

function atb_tw_source($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_', '', __FUNCTION__);
    return $atb_thistweet->$node;
}

function atb_tw_in_reply_to_status_id($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_', '', __FUNCTION__);
    return $atb_thistweet->$node;
}

function atb_tw_in_reply_to_user_id($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_', '', __FUNCTION__);
    return $atb_thistweet->$node;
}

function atb_tw_in_reply_to_screen_name($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_', '', __FUNCTION__);
    return $atb_thistweet->$node;
}

function atb_tw_user_id($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_name($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_screen_name($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_location($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_description($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_profile_image_url($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_url($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_followers_count($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_friends_count($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_created_at($atts) {
    global $atb_thistweet;
    extract(lAtts(array('format' => '',
                        'tz' => '0'),
              $atts));
    
    $time = strtotime($atb_thistweet->user->created_at);
    
    if ( !$format ) { global $prefs; $format = $prefs['archive_dateformat']; }
    
    if ( $tz === 2 ) {
        $tz = 1;
        $time += (int)$atb_thistweet->user->utc_offset;
    }
    return safe_strftime($format, $time, (int)$gmt);
}

function atb_tw_user_favourites_count($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_statuses_count($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

function atb_tw_user_lang($atts) {
    global $atb_thistweet;
    $node = str_replace('atb_tw_user_', '', __FUNCTION__);
    return $atb_thistweet->user->$node;
}

/* Caching functions.
  ----------------------------------------------------------------------------*/
/**
 * Get a parsed Twitter form from cache.
 *
 * @param string $hash The identifier for this Twitter form.
 * @param int $maxage Maximum oge of a cache to return. Set to 0 to get any cache, no matter how old.
 */
function atb_get_tweet_cache($hash, $maxage = 1800)
{
    global $prefs;
    $cachefile = $prefs['tempdir'].'/atb_twitter_'.$hash.'.txt';
    
    $mtime = file_exists($cachefile) ? filemtime($cachefile) : 0;

    if ( $mtime &&
            ( (time() - $mtime) < $maxage || $maxage == 0 ) ) {
        //The cache exists and is new enough.
        return file_get_contents($cachefile);
    }
    
    return false;
}

function atb_put_tweet_cache($hash, $data)
{
    global $prefs;
    $cachefile = $prefs['tempdir'].'/atb_twitter_'.$hash.'.txt';
    return file_put_contents($cachefile, $data);
}

/* Network plugins
  ----------------------------------------------------------------------------*/
/**
 * Grabs the twitter feed we need, either from Twitter or our local cache.
 *
 * @param string $user A screen name
 * @param int $count Number of recent tweets to retrieve
 *
 * @return bool|SimpleXMLElement The twitter response as a SimpleXML object or false on error.
 */
function atb_get_tweets($user, $count, $api_base = 'twitter.com')
{
    $url = "http://$api_base/statuses/user_timeline/$user.xml?count=$count";
    $tweets = @simplexml_load_file($url);
    
    return $tweets ? $tweets : false;
}

/* Utility function
  ----------------------------------------------------------------------------*/

function atb_linkify_tweet($tweet, $site_base = 'twitter.com', array $atts = null)
{
    //StatusNet handles hashtags differently, so we need to know whether
    //this is Twitter or SN.
    $using_twitter = ($site_base === 'twitter.com');
    extract($atts);
    
    $re = array();
    
    //Convert http: addresses to links:
    $re['/(https?:\/\/[^ ]+)/'] = create_function("\$matches, \$wrap = '$link_wrap', \$wrapclass = '$link_wrapclass'",
                                                  '
                                                   $wrapclass = $wrapclass ? " class=\"$wrapclass\"" : "";
                                                   $outp = "<a class=\"atb_status_weblink atb_status_link\" href=\"{$matches[0]}\">{$matches[0]}</a>";
                                                   if ( $wrap ) { $outp = tag($outp, $wrap, $wrapclass); }
                                                   return $outp;
                                                  ');
    
    //Convert @replies to links:
    $re['/(@([0-9a-zA-Z-_]+))/'] = create_function("\$matches, \$site_base = '$site_base', \$wrap = '$atreply_wrap', \$wrapclass = '$atreply_wrapclass'",
                                              '
                                               $class2 = "atb_atreply_to_".$matches[2];
                                               $wrapclass .= $wrapclass ? " ": "";
                                               $wrapclass = " class=\"$wrapclass$class2\"";
                                               $outp = "<a class=\"atb_status_atreply atb_status_link atb_atreply_to_{$matches[2]}\" href=\"http://$site_base/{$matches[2]}\">{$matches[0]}</a>";
                                               if ( $wrap ) { $outp = tag($outp, $wrap, $wrapclass); }
                                               return $outp;
                                              ');
    
    $re['/(#([a-zA-Z0-9-_]+))/'] = $using_twitter ?
                                   create_function("\$matches, \$site_base = '$site_base', \$wrap = '$hashtag_wrap', \$wrapclass = '$hashtag_wrapclass'",
                                              '
                                               $class2 = "atb_hashtag_".$matches[2];
                                               $wrapclass .= $wrapclass ? " ": "";
                                               $wrapclass = " class=\"$wrapclass$class2\"";
                                               $outp = "<a class=\"atb_status_hashtag atb_status_link atb_hashtag_{$matches[2]}\" href=\"http://search.twitter.com/search?result_type=recent&amp;q=%23{$matches[2]}\">{$matches[0]}</a>";
                                               if ( $wrap ) { $outp = tag($outp, $wrap, $wrapclass); }
                                               return $outp;
                                              ') :
                                   create_function("\$matches, \$site_base = '$site_base', \$wrap = '$hashtag_wrap', \$wrapclass = '$hashtag_wrapclass'",
                                              '
                                               $class2 = "atb_hashtag_".$matches[2];
                                               $wrapclass .= $wrapclass ? " ": "";
                                               $wrapclass = " class=\"$wrapclass$class2\"";
                                               $outp = "#<a class=\"atb_status_hashtag atb_status_link atb_hashtag_{$matches[2]}\" href=\"http://$site_base/tag/{$matches[2]}\">{$matches[2]}</a>";
                                               if ( $wrap ) { $outp = tag($outp, $wrap, $wrapclass); }
                                               return $outp;
                                              ');
    
    foreach ($re as $regex => $replace) {
        $tweet = preg_replace_callback($regex, $replace, $tweet);
    }
    
    return $tweet;
}

/* preg_replace_callback callbacks
  ----------------------------------------------------------------------------*/
  

# --- END PLUGIN CODE ---

?>
