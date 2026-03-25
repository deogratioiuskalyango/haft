/**
 * Copy to `youtube-site-config.js` (gitignored) and add BEFORE `youtube-channel.js` in pages/youtube.html:
 *   <script src="../js/youtube-site-config.js"></script>
 * Restrict your API key in Google Cloud to your site’s HTTP referrers.
 */
window.HAFT_YOUTUBE_CONFIG = {
  apiKey: "YOUR_YOUTUBE_DATA_API_KEY",
  channelId: "UCxxxxxxxxxxxxxxxxxxxxxxxxxx",
  // channelHandle: "YourHandle", // use instead of channelId if you prefer
};
