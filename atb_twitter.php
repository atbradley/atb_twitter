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

$plugin['version'] = '0.9.3.2';
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

h4. Tags for twitter forms:

Only a few of these tags take any attributes:

* *atb_tw_text* returns the tweet's text.  It takes one optional attribute, @linkify@. With @linkify@ set to 1 (the default), URLs, at-replies and hashtags are converted to links; with @linkify@ set to 0, the text will be left alone.
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
<h2><a href="http://twitter.com/<txp:atb_tw_user_screen_name />"><txp:atb_tw_user_name /> on Twitter</a></h2>
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


<txp:atb_tw_if_last_tweet>
</ul>
</txp:atb_tw_if_last_tweet>

</txp:atb_twitter>


h3. To_Do

* More/better error handling.
* Better/clearer documentation.

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
                        'api_base' => 'twitter.com',
                        'site_base' => ''
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
    
    $api_base = trim($api_base, '/ ');
    if ( !$site_base ) {
        $site_base = preg_replace('/(\/api$)/', '', $api_base);
    }
    
    $tweets = atb_get_tweets($user, $count, $api_base);
    if (!$tweets) {
        //Probable fail whale. Try using an old, outdated cache file
        //(so at least we'll have *some* content).
        $outp = atb_get_tweet_cache($hash, 0);
        return $outp ? $outp : '';
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
    extract(lAtts(array('linkify' => 1),
              $atts));
    
    if ( $linkify )  { return atb_linkify_tweet($atb_thistweet->text,
                                                $atb_thistweet['site_base']); }
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
    
    $mtime = filemtime($cachefile);

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
function atb_linkify_tweet($tweet, $site_base = 'twitter.com')
{
    //StatusNet handles hashtags differently, so we need to know whether
    //this is Twitter or SN.
    $using_twitter = ($api_base === 'twitter.com');
    
    $re = array();
    //Convert http: addresses to links:
    $re['/(https?:\/\/[^ ]+)/'] = '<a href="$1">$1</a>';
    
    //Convert @replies to links:
    $re['/(@([0-9a-zA-Z-_]+))/'] = "<a href=\"http://$site_base/$2\">$1</a>";
    
    //Convert hashtags to links:
    $re['/(#([a-zA-Z0-9-_]+))/'] = $using_twitter ?
        '<a href="http://search.twitter.com/search?result_type=recent&amp;q=%23$2">$1</a>' :
        "#<a href=\"http://$site_base/tag/$2\">$2</a>";
    
    foreach ($re as $regex => $replace) {
        $tweet = preg_replace($regex, $replace, $tweet);
    }
    
    return $tweet;
}

# --- END PLUGIN CODE ---

?>
