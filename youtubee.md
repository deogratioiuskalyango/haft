# Channels

The API now supports the ability to mark your channel or [videos](https://developers.google.com/youtube/v3/docs/videos) as "made for kids." In addition, `channel` and `video` resources also now contain a property that identifies the "made for kids" status of that channel or video. The YouTube API Services Terms of Service and Developer Policies were also updated on 10 January 2020. For more information, see the revision histories for the [YouTube Data API Service](https://developers.google.com/youtube/v3/revision_history) and the [YouTube API Services Terms of Service](https://developers.google.com/youtube/terms/revision-history). A `channel` resource contains information about a YouTube channel.

## Methods

The API supports the following methods for `channels` resources:

[list](https://developers.google.com/youtube/v3/docs/channels/list)
:   Returns a collection of zero or more `channel` resources that match the request criteria.
    [Try it now](https://developers.google.com/youtube/v3/docs/channels/list#usage).

[update](https://developers.google.com/youtube/v3/docs/channels/update)
:   Updates a channel's metadata. Note that this method currently only supports updates to the `channel` resource's `brandingSettings` and `invideoPromotion` objects and their child properties.
    [Try it now](https://developers.google.com/youtube/v3/docs/channels/update#usage).

## Resource representation

The following JSON structure shows the format of a `channels` resource:

```
{
  "https://developers.google.com/youtube/v3/docs/channels#kind": "youtube#channel",
  "https://developers.google.com/youtube/v3/docs/channels#etag": etag,
  "https://developers.google.com/youtube/v3/docs/channels#id": string,
  "https://developers.google.com/youtube/v3/docs/channels#snippet": {
    "https://developers.google.com/youtube/v3/docs/channels#snippet.title": string,
    "https://developers.google.com/youtube/v3/docs/channels#snippet.description": string,
    "https://developers.google.com/youtube/v3/docs/channels#snippet.customUrl": string,
    "https://developers.google.com/youtube/v3/docs/channels#snippet.publishedAt": datetime,
    "https://developers.google.com/youtube/v3/docs/channels#snippet.thumbnails": {
      (key): {
        "https://developers.google.com/youtube/v3/docs/channels#snippet.thumbnails.(key).url": string,
        "https://developers.google.com/youtube/v3/docs/channels#snippet.thumbnails.(key).width": unsigned integer,
        "https://developers.google.com/youtube/v3/docs/channels#snippet.thumbnails.(key).height": unsigned integer
      }
    },
    "https://developers.google.com/youtube/v3/docs/channels#snippet.defaultLanguage": string,
    "https://developers.google.com/youtube/v3/docs/channels#snippet.localized": {
      "https://developers.google.com/youtube/v3/docs/channels#snippet.localized.title": string,
      "https://developers.google.com/youtube/v3/docs/channels#snippet.localized.description": string
    },
    "https://developers.google.com/youtube/v3/docs/channels#snippet.country": string
  },
  "https://developers.google.com/youtube/v3/docs/channels#contentDetails": {
    "https://developers.google.com/youtube/v3/docs/channels#contentDetails.relatedPlaylists": {
      "https://developers.google.com/youtube/v3/docs/channels#contentDetails.relatedPlaylists.likes": string,
      "https://developers.google.com/youtube/v3/docs/channels#contentDetails.relatedPlaylists.favorites": string,
      "https://developers.google.com/youtube/v3/docs/channels#contentDetails.relatedPlaylists.uploads": string
    }
  },
  "https://developers.google.com/youtube/v3/docs/channels#statistics": {
    "https://developers.google.com/youtube/v3/docs/channels#statistics.viewCount": unsigned long,
    "https://developers.google.com/youtube/v3/docs/channels#statistics.subscriberCount": unsigned long,  // this value is rounded to three significant figures
    "https://developers.google.com/youtube/v3/docs/channels#statistics.hiddenSubscriberCount": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#statistics.videoCount": unsigned long
  },
  "https://developers.google.com/youtube/v3/docs/channels#topicDetails": {
    "https://developers.google.com/youtube/v3/docs/channels#topicDetails.topicIds[]": [
      string
    ],
    "https://developers.google.com/youtube/v3/docs/channels#topicDetails.topicCategories[]": [
      string
    ]
  },
  "https://developers.google.com/youtube/v3/docs/channels#status": {
    "https://developers.google.com/youtube/v3/docs/channels#status.privacyStatus": string,
    "https://developers.google.com/youtube/v3/docs/channels#status.isLinked": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#status.longUploadsStatus": string,
    "https://developers.google.com/youtube/v3/docs/channels#status.madeForKids": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#status.selfDeclaredMadeForKids": boolean
  },
  "https://developers.google.com/youtube/v3/docs/channels#brandingSettings": {
    "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel": {
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.title": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.description": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.keywords": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.trackingAnalyticsAccountId": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.unsubscribedTrailer": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.defaultLanguage": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.country": string
    },
    "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.watch": {
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.watch.textColor": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.watch.backgroundColor": string,
      "https://developers.google.com/youtube/v3/docs/channels#brandingSettings.watch.featuredPlaylistId": string
    }
  },
  "https://developers.google.com/youtube/v3/docs/channels#auditDetails": {
    "https://developers.google.com/youtube/v3/docs/channels#auditDetails.overallGoodStanding": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#auditDetails.communityGuidelinesGoodStanding": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#auditDetails.copyrightStrikesGoodStanding": boolean,
    "https://developers.google.com/youtube/v3/docs/channels#auditDetails.contentIdClaimsGoodStanding": boolean
  },
  "https://developers.google.com/youtube/v3/docs/channels#contentOwnerDetails": {
    "https://developers.google.com/youtube/v3/docs/channels#contentOwnerDetails.contentOwner": string,
    "https://developers.google.com/youtube/v3/docs/channels#contentOwnerDetails.timeLinked": datetime
  },
  "https://developers.google.com/youtube/v3/docs/channels#localizations": {
    (key): {
      "https://developers.google.com/youtube/v3/docs/channels#localizations.(key).title": string,
      "https://developers.google.com/youtube/v3/docs/channels#localizations.(key).description": string
    }
  }
}
```

### Properties

The following table defines the properties that appear in this resource:

| Properties ||
|---|---|
| `kind` | `string` Identifies the API resource's type. The value will be `youtube#channel`. |
| `etag` | `etag` The Etag of this resource. |
| `id` | `string` The ID that YouTube uses to uniquely identify the channel. |
| `snippet` | `object` The `snippet` object contains basic details about the channel, such as its title, description, and thumbnail images. |
| `snippet.title` | `string` The channel's title. |
| `snippet.description` | `string` The channel's description. The property's value has a maximum length of 1000 characters. |
| `snippet.customUrl` | `string` The channel's custom URL. The [YouTube Help Center](https://support.google.com/youtube/answer/2657968) explains eligibility requirements for getting a custom URL as well as how to set up the URL. |
| `snippet.publishedAt` | `datetime` The date and time that the channel was created. The value is specified in [ISO 8601](https://www.w3.org/TR/NOTE-datetime) format. |
| `snippet.thumbnails` | `object` A map of thumbnail images associated with the channel. For each object in the map, the key is the name of the thumbnail image, and the value is an object that contains other information about the thumbnail. When displaying thumbnails in your application, make sure that your code uses the image URLs exactly as they are returned in API responses. For example, your application should not use the `http` domain instead of the `https` domain in a URL returned in an API response. Channel thumbnail URLs are available only in the `https` domain, which is how the URLs appear in API responses. You might see broken images in your application if it tries to load YouTube images from the `http` domain. Thumbnail images might be empty for newly created channels and might take up to one day to populate. |
| `snippet.thumbnails.(key)` | `object` Valid key values are: - `default` -- The default thumbnail image. The default thumbnail for a video -- or a resource that refers to a video, such as a playlist item or search result -- is 120px wide and 90px tall. The default thumbnail for a channel is 88px wide and 88px tall. - `medium` -- A higher resolution version of the thumbnail image. For a video (or a resource that refers to a video), this image is 320px wide and 180px tall. For a channel, this image is 240px wide and 240px tall. - `high` -- A high resolution version of the thumbnail image. For a video (or a resource that refers to a video), this image is 480px wide and 360px tall. For a channel, this image is 800px wide and 800px tall. |
| `snippet.thumbnails.(key).url` | `string` The image's URL. For additional guidelines on using thumbnail URLs in your application, see the `https://developers.google.com/youtube/v3/docs/channels#snippet.thumbnails` property definition. |
| `snippet.thumbnails.(key).width` | `unsigned integer` The image's width. |
| `snippet.thumbnails.(key).height` | `unsigned integer` The image's height. |
| `snippet.defaultLanguage` | `string` The language of the text in the `channel` resource's `snippet.title` and `snippet.description` properties. |
| `snippet.localized` | `object` The `snippet.localized` object contains a localized title and description for the channel or it contains the channel's title and description in the [default language](https://developers.google.com/youtube/v3/docs/channels#snippet.defaultLanguage) for the channel's metadata. - Localized text is returned in the resource snippet if the `https://developers.google.com/youtube/v3/docs/channels/list` request used the `hl` parameter to specify a language for which localized text should be returned, the `hl` parameter value identifies a [YouTube application language](https://developers.google.com/youtube/v3/docs/i18nLanguages), and localized text is available in that language. - Metadata for the default language is returned if an `hl` parameter value is not specified *or* a value is specified but localized metadata is not available for the specified language. The property contains a read-only value. Use the `https://developers.google.com/youtube/v3/docs/channels#localizations` object to add, update, or delete localized metadata. |
| `snippet.localized.title` | `string` The localized channel title. |
| `snippet.localized.description` | `string` The localized channel description. |
| `snippet.country` | `string` The country with which the channel is associated. To set this property's value, update the value of the `https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.country` property. |
| `contentDetails` | `object` The `contentDetails` object encapsulates information about the channel's content. |
| `contentDetails.relatedPlaylists` | `object` The `relatedPlaylists` object is a map that identifies playlists associated with the channel, such as the channel's uploaded videos or liked videos. You can retrieve any of these playlists using the `https://developers.google.com/youtube/v3/docs/playlists/list` method. |
| `contentDetails.relatedPlaylists.likes` | `string` The ID of the playlist that contains the channel's liked videos. Use the `https://developers.google.com/youtube/v3/docs/playlistItems/insert` and `https://developers.google.com/youtube/v3/docs/playlistItems/delete` methods to add or remove items from that list. |
| `contentDetails.relatedPlaylists.favorites` | `string` This property has been deprecated. The ID of the playlist that contains the channel's favorite videos. Use the `https://developers.google.com/youtube/v3/docs/playlistItems/insert` and `https://developers.google.com/youtube/v3/docs/playlistItems/delete` methods to add or remove items from that list. Note that YouTube has deprecated favorite video functionality. For example, the `video` resource's `statistics.favoriteCount` property was deprecated on August 28, 2015. As a result, for historical reasons, this property value might contain a playlist ID that refers to an empty playlist and, therefore, cannot be fetched. |
| `contentDetails.relatedPlaylists.uploads` | `string` The ID of the playlist that contains the channel's uploaded videos. Use the `https://developers.google.com/youtube/v3/docs/videos/insert` method to upload new videos and the `https://developers.google.com/youtube/v3/docs/videos/delete` method to delete previously uploaded videos. |
| `statistics` | `object` The `statistics` object encapsulates statistics for the channel. |
| `statistics.viewCount` | `unsigned long` The sum of the number of times all the videos in all formats have been viewed for a channel. Starting March 31, 2025, for Shorts on a channel, viewCount will be updated to include the number of times a Short starts to play or replay. |
| `statistics.commentCount` | `unsigned long` This property has been deprecated. The number of comments for the channel. |
| `statistics.subscriberCount` | `unsigned long` The number of subscribers that the channel has. This value is rounded down to three significant figures. For more details about how subscriber counts are rounded, see [Revision History](https://developers.google.com/youtube/v3/revision_history#release_notes_09_10_2019) or [YouTube Help Center](https://support.google.com/youtube/answer/6051134). |
| `statistics.hiddenSubscriberCount` | `boolean` Indicates whether the channel's subscriber count is publicly visible. |
| `statistics.videoCount` | `unsigned long` The number of public videos uploaded to the channel. Note that the value reflects the count of the channel's public videos only, even to owners. This behavior is consistent with counts shown on the YouTube website. |
| `topicDetails` | `object` The `topicDetails` object encapsulates information about topics associated with the channel. **Important:** For more details about changes related to topic IDs, see the `topicDetails.topicIds[]` property definition and the [revision history](https://developers.google.com/youtube/v3/revision_history#november-10-2016). |
| `topicDetails.topicIds[]` | `list` A list of topic IDs associated with the channel. This property has been deprecated as of November 10, 2016. It will be supported until November 10, 2017. **Important:** Due to the deprecation of Freebase and the Freebase API, topic IDs started working differently as of February 27, 2017. At that time, YouTube started returning a small set of curated topic IDs. [See topic IDs supported as of February 15, 2017](https://developers.google.com/youtube/v3/docs/channels#) | Topics || |---|---| | **Music topics** || | /m/04rlf | Music (parent topic) | | /m/02mscn | Christian music | | /m/0ggq0m | Classical music | | /m/01lyv | Country | | /m/02lkt | Electronic music | | /m/0glt670 | Hip hop music | | /m/05rwpb | Independent music | | /m/03_d0 | Jazz | | /m/028sqc | Music of Asia | | /m/0g293 | Music of Latin America | | /m/064t9 | Pop music | | /m/06cqb | Reggae | | /m/06j6l | Rhythm and blues | | /m/06by7 | Rock music | | /m/0gywn | Soul music | | **Gaming topics** || | /m/0bzvm2 | Gaming (parent topic) | | /m/025zzc | Action game | | /m/02ntfj | Action-adventure game | | /m/0b1vjn | Casual game | | /m/02hygl | Music video game | | /m/04q1x3q | Puzzle video game | | /m/01sjng | Racing video game | | /m/0403l3g | Role-playing video game | | /m/021bp2 | Simulation video game | | /m/022dc6 | Sports game | | /m/03hf_rm | Strategy video game | | **Sports topics** || | /m/06ntj | Sports (parent topic) | | /m/0jm_ | American football | | /m/018jz | Baseball | | /m/018w8 | Basketball | | /m/01cgz | Boxing | | /m/09xp_ | Cricket | | /m/02vx4 | Football | | /m/037hz | Golf | | /m/03tmr | Ice hockey | | /m/01h7lh | Mixed martial arts | | /m/0410tth | Motorsport | | /m/07bs0 | Tennis | | /m/07_53 | Volleyball | | **Entertainment topics** || | /m/02jjt | Entertainment (parent topic) | | /m/09kqc | Humor | | /m/02vxn | Movies | | /m/05qjc | Performing arts | | /m/066wd | Professional wrestling | | /m/0f2f9 | TV shows | | **Lifestyle topics** || | /m/019_rr | Lifestyle (parent topic) | | /m/032tl | Fashion | | /m/027x7n | Fitness | | /m/02wbm | Food | | /m/03glg | Hobby | | /m/068hy | Pets | | /m/041xxh | Physical attractiveness \[Beauty\] | | /m/07c1v | Technology | | /m/07bxq | Tourism | | /m/07yv9 | Vehicles | | **Society topics** || | /m/098wr | Society (parent topic) | | /m/09s1f | Business | | /m/0kt51 | Health | | /m/01h6rj | Military | | /m/05qt0 | Politics | | /m/06bvp | Religion | | **Other topics** || | /m/01k8wb | Knowledge | |
| `topicDetails.topicCategories[]` | `list` A list of Wikipedia URLs that describe the channel's content. |
| `status` | `object` The `status` object encapsulates information about the privacy status of the channel. |
| `status.privacyStatus` | `string` Privacy status of the channel. Valid values for this property are: - `private` - `public` - `unlisted` |
| `status.isLinked` | `boolean` Indicates whether the channel data identifies a user that is already linked to either a YouTube username or a Google+ account. A user that has one of these links already has a public YouTube identity, which is a prerequisite for several actions, such as uploading videos. |
| `status.longUploadsStatus` | `string` Indicates whether the channel is eligible to upload videos that are more than 15 minutes long. This property is only returned if the channel owner authorized the API request. For more information about this feature, see [YouTube Help Center](https://support.google.com/youtube/answer/71673). Valid values for this property are: - `allowed` -- This channel can upload videos that are more than 15 minutes long. - `disallowed` -- This channel is not able or eligible to upload videos that are more than 15 minutes long. A channel is only eligible to upload long videos if it is in good standing based on [YouTube Community Guidelines](http://www.youtube.com/t/community_guidelines) and it does not have any worldwide Content ID blocks on its content. After the channel owner resolves the issues that are preventing the channel from uploading longer videos, the channel will revert to either the `allowed` or `eligible` state. - `eligible` -- This channel is eligible to upload videos that are more than 15 minutes long. However, the channel owner must first enable the ability to upload longer videos through [Phone verification](https://www.youtube.com/verify). For more details about this feature, see [YouTube Help Center](https://support.google.com/youtube/answer/71673). |
| `status.madeForKids` | `boolean` This value indicates whether the channel is designated as child-directed, and it contains the current "made for kids" status of the channel. For example, the status might be determined based on the value of the `selfDeclaredMadeForKids` property. For more information about setting the audience for your channel, videos, or broadcasts, see [YouTube Help Center](https://support.google.com/youtube/answer/9527654) . |
| `status.selfDeclaredMadeForKids` | `boolean` In a `https://developers.google.com/youtube/v3/docs/channels/update` request, this property allows the channel owner to designate the channel as child-directed. The property value is only returned if the channel owner authorized the API request. |
| `brandingSettings` | `object` The `brandingSettings` object encapsulates information about the branding of the channel. |
| `brandingSettings.channel` | `object` The `channel` object encapsulates branding properties of the channel page. |
| `brandingSettings.channel.title` | `string` The channel's title. The title has a maximum length of 30 characters. |
| `brandingSettings.channel.description` | `string` The channel description, which appears in the channel information box on your channel page. The property's value has a maximum length of 1000 characters. |
| `brandingSettings.channel.keywords` | `string` Keywords associated with your channel. The value is a space-separated list of strings. Channel keywords might be truncated if they exceed the maximum allowed length of 500 characters or if they contained unescaped quotation marks (`"`). Note that the 500 character limit is not a per-keyword limit but rather a limit on the total length of all keywords. |
| `brandingSettings.channel.trackingAnalyticsAccountId` | `string` The ID for a [Google Analytics account](http://www.google.com/analytics/index.html) that you want to use to track and measure traffic to your channel. |
| `brandingSettings.channel.unsubscribedTrailer` | `string` The video that should play in the featured video module in the channel page's browse view for unsubscribed viewers. Subscribed viewers may see a different video that highlights more recent channel activity. If specified, the property's value must be the YouTube video ID of a public or unlisted video that is owned by the channel owner. |
| `brandingSettings.channel.defaultLanguage` | `string` The language of the text in the `channel` resource's `snippet.title` and `snippet.description` properties. |
| `brandingSettings.channel.country` | `string` The country with which the channel is associated. Update this property to set the value of the `https://developers.google.com/youtube/v3/docs/channels#snippet.country` property. |
| `brandingSettings.watch` | `object` **Note:** This object and all of its child properties have been deprecated. The `watch` object encapsulates branding properties of the watch pages for the channel's videos. |
| `brandingSettings.watch.textColor` | `string` **Note:** This property has been deprecated. The text color for the video watch page's branded area. |
| `brandingSettings.watch.backgroundColor` | `string` **Note:** This property has been deprecated. The background color for the video watch page's branded area. |
| `brandingSettings.watch.featuredPlaylistId` | `string` **Note:** This property has been deprecated. The API returns an error if you attempt to set its value. |
| `brandingSettings.image` | `object` This property and all of its child properties have been deprecated. The `image` object encapsulates information about images that display on the channel's channel page or video watch pages. |
| `brandingSettings.image.bannerImageUrl` | `string` This property has been deprecated. The URL for the banner image shown on the channel page on the YouTube website. The image is 1060px by 175px. |
| `brandingSettings.image.bannerMobileImageUrl` | `string` This property has been deprecated. The URL for the banner image shown on the channel page in mobile applications. The image is 640px by 175px. |
| `brandingSettings.image.watchIconImageUrl` | `string` This property has been deprecated. The URL for the image that appears above the video player. This is a 25-pixel-high image with a flexible width that cannot exceed 170 pixels. If you do not provide this image, your channel name will appear instead of an image. |
| `brandingSettings.image.trackingImageUrl` | `string` This property has been deprecated. The URL for a 1px by 1px tracking pixel that can be used to collect statistics for views of the channel or video pages. |
| `brandingSettings.image.bannerTabletLowImageUrl` | `string` This property has been deprecated. The URL for a low-resolution banner image that displays on the channel page in tablet applications. The image's maximum size is 1138px by 188px. |
| `brandingSettings.image.bannerTabletImageUrl` | `string` This property has been deprecated. The URL for a banner image that displays on the channel page in tablet applications. The image is 1707px by 283px. |
| `brandingSettings.image.bannerTabletHdImageUrl` | `string` This property has been deprecated. The URL for a high-resolution banner image that displays on the channel page in tablet applications. The image's maximum size is 2276px by 377px. |
| `brandingSettings.image.bannerTabletExtraHdImageUrl` | `string` This property has been deprecated. The URL for an extra-high-resolution banner image that displays on the channel page in tablet applications. The image's maximum size is 2560px by 424px. |
| `brandingSettings.image.bannerMobileLowImageUrl` | `string` This property has been deprecated. The URL for a low-resolution banner image that displays on the channel page in mobile applications. The image's maximum size is 320px by 88px. |
| `brandingSettings.image.bannerMobileMediumHdImageUrl` | `string` This property has been deprecated. The URL for a medium-resolution banner image that displays on the channel page in mobile applications. The image's maximum size is 960px by 263px. |
| `brandingSettings.image.bannerMobileHdImageUrl` | `string` This property has been deprecated. The URL for a high-resolution banner image that displays on the channel page in mobile applications. The image's maximum size is 1280px by 360px. |
| `brandingSettings.image.bannerMobileExtraHdImageUrl` | `string` This property has been deprecated. The URL for a very high-resolution banner image that displays on the channel page in mobile applications. The image's maximum size is 1440px by 395px. |
| `brandingSettings.image.bannerTvImageUrl` | `string` This property has been deprecated. The URL for an extra-high-resolution banner image that displays on the channel page in television applications. The image's maximum size is 2120px by 1192px. |
| `brandingSettings.image.bannerTvLowImageUrl` | `string` This property has been deprecated. The URL for a low-resolution banner image that displays on the channel page in television applications. The image's maximum size is 854px by 480px. |
| `brandingSettings.image.bannerTvMediumImageUrl` | `string` This property has been deprecated. The URL for a medium-resolution banner image that displays on the channel page in television applications. The image's maximum size is 1280px by 720px. |
| `brandingSettings.image.bannerTvHighImageUrl` | `string` This property has been deprecated. The URL for a high-resolution banner image that displays on the channel page in television applications. The image's maximum size is 1920px by 1080px. |
| `brandingSettings.image.bannerExternalUrl` | `string` This property specifies the location of the banner image that YouTube uses to generate the various banner image sizes for a channel. |
| `brandingSettings.hints[]` | `list` This property and all of its child properties have been deprecated. The `hints` object encapsulates additional branding properties. |
| `brandingSettings.hints[].property` | `string` This property has been deprecated. A property. |
| `brandingSettings.hints[].value` | `string` This property has been deprecated. The property's value. |
| `auditDetails` | `object` The `auditDetails` object encapsulates channel data that a multichannel network (MCN) would evaluate while determining whether to accept or reject a particular channel. Note that any API request that retrieves this resource part must provide an authorization token that contains the `https://www.googleapis.com/auth/youtubepartner-channel-audit` scope. In addition, any token that uses that scope must be revoked when the MCN decides to accept or reject the channel or within two weeks of the date that the token was issued. |
| `auditDetails.overallGoodStanding` | `boolean` This field indicates whether there are any issues with the channel. Currently, this field represents the result of the logical `AND` operation over the `communityGuidelinesGoodStanding`, `copyrightStrikesGoodStanding`, and `contentIdClaimsGoodStanding` properties, meaning that this property has a value of `true` if all of those other properties also have a value of `true`. However, this property will have a value of `false` if any of those properties has a value of `false`. Note, however, that the methodology used to set this property's value is subject to change. |
| `auditDetails.communityGuidelinesGoodStanding` | `boolean` Indicates whether the channel respects YouTube's community guidelines. |
| `auditDetails.copyrightStrikesGoodStanding` | `boolean` Indicates whether the channel has any copyright strikes. |
| `auditDetails.contentIdClaimsGoodStanding` | `boolean` Indicates whether the channel has any unresolved claims. |
| `contentOwnerDetails` | `object` The `contentOwnerDetails` object encapsulates channel data that is visible only to the YouTube Partner that has linked the channel to their Content Manager. |
| `contentOwnerDetails.contentOwner` | `string` The ID of the content owner linked to the channel. |
| `contentOwnerDetails.timeLinked` | `datetime` The date and time of when the channel was linked to the content owner. The value is specified in [ISO 8601](https://www.w3.org/TR/NOTE-datetime) format. |
| `localizations` | `object` The `localizations` object encapsulates translations of the channel's metadata. |
| `localizations.(key)` | `object` The language of the localized metadata associated with the key value. The value is a string that contains a [BCP-47](http://www.rfc-editor.org/rfc/bcp/bcp47.txt) language code. |
| `localizations.(key).title` | `string` The localized channel title. |
| `localizations.(key).description` | `string` The localized channel description. |







(The YouTube Data API lets you incorporate functions normally executed on the YouTube website into your own website or application. The following sections identify the different types of resources that you can retrieve using the API. The API also supports methods to insert, update, or delete many of these resources.

This reference guide explains how to use the API to perform all of these operations. The guide is organized by resource type. A resource represents a type of item that comprises part of the YouTube experience, such as a video, a playlist, or a subscription. For each resource type, the guide lists one or more data representations, and resources are represented as JSON objects. The guide also lists one or more supported methods (`LIST`, `POST`, `DELETE`, etc.) for each resource type and explains how to use those methods in your application.

## Call the API

The following requirements apply to YouTube Data API requests:

1. Every request must either specify an API key (with the `key` parameter) or provide an OAuth 2.0 token. Your API key is available in the [Developer Console's](https://console.developers.google.com/) **API Access** pane for your project.

2. You **must** send an authorization token for every insert, update, and delete request. You must also send an authorization token for any request that retrieves the authenticated user's private data.

   In addition, some API methods for retrieving resources may support parameters that require authorization or may contain additional metadata when requests are authorized. For example, a request to retrieve a user's uploaded videos may also contain private videos if the request is authorized by that specific user.
3. The API supports the OAuth 2.0 authentication protocol. You can provide an OAuth 2.0 token in either of the following ways:

   - Use the `access_token` query parameter like this: `?access_token=``oauth2-token`
   - Use the HTTP `Authorization` header like this: `Authorization: Bearer` `oauth2-token`

   Complete instructions for implementing OAuth 2.0 authentication in your application can be found in the [authentication guide](https://developers.google.com/youtube/v3/guides/authentication).

## Resource types

### Activities

An `activity` resource contains information about an action that a particular channel, or user, has taken on YouTube. The actions reported in activity feeds include rating a video, sharing a video, marking a video as a favorite, uploading a video, and so forth. Each `activity` resource identifies the type of action, the channel associated with the action, and the resource(s) associated with the action, such as the video that was rated or uploaded.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/activities#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/activities#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/activities/list` | `GET /activities` | Returns a list of channel activity events that match the request criteria. For example, you can retrieve events associated with a particular channel or with the user's own channel. |

### Captions

A `caption` resource represents a YouTube caption track. A caption track is associated with exactly one YouTube video.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/captions#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/captions#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/captions/delete` | `DELETE /captions` | Deletes the specified caption track. |
| `https://developers.google.com/youtube/v3/docs/captions/download` | `GET /captions/id` | Downloads a caption track. The caption track is returned in its original format unless the request specifies a value for the `tfmt` parameter and in its original language unless the request specifies a value for the `tlang` parameter. |
| `https://developers.google.com/youtube/v3/docs/captions/insert` | `POST /captions` | Uploads a caption track. |
| `https://developers.google.com/youtube/v3/docs/captions/list` | `GET /captions` | Returns a list of caption tracks that are associated with a specified video. The API response does not contain the actual captions and that the `https://developers.google.com/youtube/v3/docs/captions/download` method provides the ability to retrieve a caption track. |
| `https://developers.google.com/youtube/v3/docs/captions/update` | `PUT /captions` | Updates a caption track. When updating a caption track, you can change the track's [draft status](https://developers.google.com/youtube/v3/docs/captions#snippet.isDraft), upload a new caption file for the track, or both. |

### ChannelBanners

A `channelBanner` resource contains the URL that you would use to set a newly uploaded image as the banner image for a channel.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/channelBanners#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/channelBanners#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/channelBanners/insert` | `POST /channelBanners/insert` | Uploads a channel banner image to YouTube. This method represents the first two steps in a three-step process to update the banner image for a channel: 1. Call the `channelBanners.insert` method to upload the binary image data to YouTube. The image must have a 16:9 aspect ratio and be at least 2048x1152 pixels. We recommend uploading a 2560px by 1440px image. 2. Extract the `url` property's value from the response that the API returns for step 1. 3. Call the `https://developers.google.com/youtube/v3/docs/channels/update` method to update the channel's branding settings. Set the `https://developers.google.com/youtube/v3/docs/channels#brandingSettings.image.bannerExternalUrl` property's value to the URL obtained in step 2. |

### ChannelSections

A `channelSection` resource contains information about a set of videos that a channel has chosen to feature. For example, a section could feature a channel's latest uploads, most popular uploads, or videos from one or more playlists.  

A channel's sections are only visible if the channel displays content in a browse view (rather than a feed view). To enable a channel to display content in a browse view, set the `https://developers.google.com/youtube/v3/docs/channels#brandingSettings.channel.showBrowseView` property to `true` for the specified channel.  

A channel can create a maximum of 10 shelves.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/channelSections#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/channelSections#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/channelSections/delete` | `DELETE /channelSections` | Deletes a channel section. |
| `https://developers.google.com/youtube/v3/docs/channelSections/insert` | `POST /channelSections` | Adds a channel section to the authenticated user's channel. A channel can create a maximum of 10 shelves. |
| `https://developers.google.com/youtube/v3/docs/channelSections/list` | `GET /channelSections` | Returns a list of `channelSection` resources that match the API request criteria. |
| `https://developers.google.com/youtube/v3/docs/channelSections/update` | `PUT /channelSections` | Updates a channel section. |

### Channels

A `channel` resource contains information about a YouTube channel.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/channels#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/channels#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/channels/list` | `GET /channels` | Returns a collection of zero or more `channel` resources that match the request criteria. |
| `https://developers.google.com/youtube/v3/docs/channels/update` | `PUT /channels` | Updates a channel's metadata. This method only supports updates to the `channel` resource's `brandingSettings` and `invideoPromotion` objects and their child properties. |

### CommentThreads

A `commentThread` resource contains information about a YouTube comment thread, which comprises a top-level comment and replies, if any exist, to that comment. A `commentThread` resource can represent comments about either a video or a channel.  

Both the top-level comment and the replies are actually `https://developers.google.com/youtube/v3/docs/comments` resources nested inside the `commentThread` resource. The `commentThread` resource does not necessarily contain all replies to a comment, and you need to use the `https://developers.google.com/youtube/v3/docs/comments/list` method if you want to retrieve all replies for a particular comment. Also, some comments don't have replies.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/commentThreads#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/commentThreads#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/commentThreads/list` | `GET /commentThreads` | Returns a list of comment threads that match the API request parameters. |
| `https://developers.google.com/youtube/v3/docs/commentThreads/insert` | `POST /commentThreads` | Creates a new top-level comment. To add a reply to an existing comment, use the `https://developers.google.com/youtube/v3/docs/comments/insert` method instead. |

### Comments

A `comment` resource contains information about a single YouTube comment. A `comment` resource can represent a comment about either a video or a channel. In addition, the comment could be a top-level comment or a reply to a top-level comment.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/comments#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/comments#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/comments/list` | `GET /comments` | Returns a list of comments that match the API request parameters. |
| `https://developers.google.com/youtube/v3/docs/comments/setModerationStatus` | `POST /comments/setModerationStatus` | Sets the moderation status of one or more comments. The API request must be authorized by the owner of the channel or video associated with the comments. |
| `https://developers.google.com/youtube/v3/docs/comments/insert` | `POST /comments` | Creates a reply to an existing comment. **Note:** To create a top-level comment, use the `https://developers.google.com/youtube/v3/docs/commentThreads/insert` method. |
| `https://developers.google.com/youtube/v3/docs/comments/delete` | `DELETE /comments` | Deletes a comment. |
| `https://developers.google.com/youtube/v3/docs/comments/update` | `PUT /comments` | Modifies a comment. |

### I18nLanguages

An `i18nLanguage` resource identifies an application language that the YouTube website supports. The application language can also be referred to as a UI language. For the YouTube website, an application language could be automatically selected based on Google Account settings, browser language, or IP location. A user could also manually select the UI language from the YouTube site footer.  

Each `i18nLanguage` resource identifies a language code and a name. The language code can be used as the value of the `hl` parameter when calling API methods like `videoCategories.list`.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/i18nLanguages#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/i18nLanguages#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/i18nLanguages/list` | `GET /i18nLanguages` | Returns a list of application languages that the YouTube website supports. |

### I18nRegions

An `i18nRegion` resource identifies a geographic area that a YouTube user can select as the preferred content region. The content region can also be referred to as a content locale. For the YouTube website, a content region could be automatically selected based on heuristics like the YouTube domain or the user's IP location. A user could also manually select the content region from the YouTube site footer.  

Each `i18nRegion` resource identifies a region code and a name. The region code can be used as the value of the `regionCode` parameter when calling API methods like `search.list`, `videos.list`, `activities.list`, and `videoCategories.list`.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/i18nRegions#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/i18nRegions#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/i18nRegions/list` | `GET /i18nRegions` | Returns a list of content regions that the YouTube website supports. |

### Members

A `member` resource represents a channel member for a YouTube
channel. A member provides recurring monetary support to a creator and receives special
benefits. For example, members are able to chat when the creator turns on members-only mode for
a chat.

For more information about this resource, see its
[resource representation](https://developers.google.com/youtube/v3/docs/members#resource) and list of
[properties](https://developers.google.com/youtube/v3/docs/members#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/members/list` | `GET /members` | Lists members (formerly known as "sponsors") for a channel. The API request must be authorized by the channel owner. |

### MembershipsLevels

A `membershipsLevel` resource identifies a pricing level for the
creator that authorized the API request.

For more information about this resource, see its
[resource representation](https://developers.google.com/youtube/v3/docs/membershipsLevels#resource) and list of
[properties](https://developers.google.com/youtube/v3/docs/membershipsLevels#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/membershipsLevels/list` | `GET /membershipsLevels` | Returns a collection of zero or more `membershipsLevel` resources owned by the channel that authorized the API request. Levels are returned in implicit display order. |

### PlaylistItems

A `playlistItem` resource identifies another resource, such as a
video, that is included in a playlist. In addition, the `playlistItem ` resource
contains details about the included resource that pertain specifically to how that resource
is used in that playlist.  


YouTube also uses a playlist to identify a channel's list of uploaded videos, with each
`playlistItem` in that list representing one uploaded video. You can retrieve the
playlist ID for that list from the `https://developers.google.com/youtube/v3/docs/channels`
for a given channel. You can then use the
`https://developers.google.com/youtube/v3/docs/playlistItems/list` method to retrieve the
list.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/playlistItems#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/playlistItems#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/playlistItems/delete` | `DELETE /playlistItems` | Deletes a playlist item. |
| `https://developers.google.com/youtube/v3/docs/playlistItems/insert` | `POST /playlistItems` | Adds a resource to a playlist. |
| `https://developers.google.com/youtube/v3/docs/playlistItems/list` | `GET /playlistItems` | Returns a collection of playlist items that match the API request parameters. You can retrieve all of the playlist items in a specified playlist or retrieve one or more playlist items by their unique IDs. |
| `https://developers.google.com/youtube/v3/docs/playlistItems/update` | `PUT /playlistItems` | Modifies a playlist item. For example, you could update the item's position in the playlist. |

### Playlists

A `playlist` resource represents a YouTube playlist. A playlist is a collection of videos that can be viewed sequentially and shared with other users. By default, playlists are publicly visible to other users, but playlists can be public or private.  

YouTube also uses playlists to identify special collections of videos for a channel, such as:

- uploaded videos
- positively rated (liked) videos
- watch history
- watch later

To be more specific, these lists are associated with a channel, which is a collection of a person, group, or company's videos, playlists, and other YouTube information. You can retrieve the playlist IDs for each of these lists from the `https://developers.google.com/youtube/v3/docs/channels` for a given channel.  

You can then use the `https://developers.google.com/youtube/v3/docs/playlistItems/list` method to retrieve any of those lists. You can also add or remove items from those lists by calling the `https://developers.google.com/youtube/v3/docs/playlistItems/insert` and `https://developers.google.com/youtube/v3/docs/playlistItems/delete` methods.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/playlists#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/playlists#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/playlists/delete` | `DELETE /playlists` | Deletes a playlist. |
| `https://developers.google.com/youtube/v3/docs/playlists/list` | `GET /playlists` | Returns a collection of playlists that match the API request parameters. For example, you can retrieve all playlists that the authenticated user owns, or you can retrieve one or more playlists by their unique IDs. |
| `https://developers.google.com/youtube/v3/docs/playlists/insert` | `POST /playlists` | Creates a playlist. |
| `https://developers.google.com/youtube/v3/docs/playlists/update` | `PUT /playlists` | Modifies a playlist. For example, you could change a playlist's title, description, or privacy status. |

### Search

A search result contains information about a YouTube video, channel, or playlist that matches the search parameters specified in an API request. While a search result points to a uniquely identifiable resource, like a video, it does not have its own persistent data.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/search#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/search#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/search/list` | `GET /search` | Returns a collection of search results that match the query parameters specified in the API request. By default, a search result set identifies matching `https://developers.google.com/youtube/v3/docs/videos`, `https://developers.google.com/youtube/v3/docs/channels`, and `https://developers.google.com/youtube/v3/docs/playlists` resources, but you can also configure queries to only retrieve a specific type of resource. |

### Subscriptions

A `subscription` resource contains information about a YouTube user subscription. A subscription notifies a user when new videos are added to a channel or when another user takes one of several actions on YouTube, such as uploading a video, rating a video, or commenting on a video.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/subscriptions#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/subscriptions#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/subscriptions/delete` | `DELETE /subscriptions` | Deletes a subscription. |
| `https://developers.google.com/youtube/v3/docs/subscriptions/insert` | `POST /subscriptions` | Adds a subscription for the authenticated user's channel. |
| `https://developers.google.com/youtube/v3/docs/subscriptions/list` | `GET /subscriptions` | Returns subscription resources that match the API request criteria. |

### Thumbnails

A `thumbnail` resource identifies different thumbnail image sizes associated with a resource. Thumbnail images have the following characteristics:

- A resource's `snippet.thumbnails` property is an object that identifies the thumbnail images available for that resource.
- A `thumbnail` resource contains a series of objects. The name of each object (`default`, `medium`, `high`, etc.) refers to the thumbnail image size.
- Different types of resources may support different thumbnail image sizes.
- Different types of resources may define different sizes for thumbnail images with the same name. For example, the `default` thumbnail image for a `video` resource is typically 120px by 90px, and the `default` thumbnail image for a `channel` resource is typically 88px by 88px.
- Resources of the same type may still have different thumbnail image sizes for certain images depending on the resolution of the original image or content uploaded to YouTube. For example, an HD video may support higher resolution thumbnails than non-HD videos.
- Each object that contains information about a thumbnail image size has a `width` property and a `height` property. However, the width and height properties may not be returned for that image.
- If an uploaded thumbnail image does not match the required dimensions, the image is resized to match the correct size without changing its aspect ratio. The image is not cropped, but may include black bars so that the size is correct.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/thumbnails#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/thumbnails#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/thumbnails/set` | `POST /thumbnails/set` | Uploads a custom video thumbnail to YouTube and sets it for a video. |

### VideoAbuseReportReasons

A `videoAbuseReportReason` resource contains information about a reason that a video would be flagged for containing abusive content. When your application calls the `https://developers.google.com/youtube/v3/docs/videos/reportAbuse` method to report an abusive video, the request uses the information from a `videoAbuseReportReason` resource to identify the reason that the video is being reported.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/videoAbuseReportReasons#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/videoAbuseReportReasons#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/videoAbuseReportReasons/list` | `GET /videoAbuseReportReasons` | Retrieve a list of reasons that can be used to report abusive videos. |

### VideoCategories

A `videoCategory` resource identifies a category that has been or could be associated with uploaded videos.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/videoCategories#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/videoCategories#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/videoCategories/list` | `GET /videoCategories` | Returns a list of categories that can be associated with YouTube videos. |

### Videos

A `video` resource represents a YouTube video.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/videos#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/videos#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/videos/insert` | `POST /videos` | Uploads a video to YouTube and optionally sets the video's metadata. |
| `https://developers.google.com/youtube/v3/docs/videos/list` | `GET /videos` | Returns a list of videos that match the API request parameters. |
| `https://developers.google.com/youtube/v3/docs/videos/delete` | `DELETE /videos` | Deletes a YouTube video. |
| `https://developers.google.com/youtube/v3/docs/videos/update` | `PUT /videos` | Updates a video's metadata. |
| `https://developers.google.com/youtube/v3/docs/videos/rate` | `POST /videos/rate` | Add a like or dislike rating to a video or remove a rating from a video. |
| `https://developers.google.com/youtube/v3/docs/videos/getRating` | `GET /videos/getRating` | Retrieves the ratings that the authorized user gave to a list of specified videos. |
| `https://developers.google.com/youtube/v3/docs/videos/reportAbuse` | `POST /videos/reportAbuse` | Report a video for containing abusive content. |

### Watermarks

A `watermark` resource identifies an image that displays during playbacks of a specified channel's videos. You can also specify a target channel to which the image will link as well as timing details that determine when the watermark appears during video playbacks and the length of time it is visible.

For more information about this resource, see its [resource representation](https://developers.google.com/youtube/v3/docs/watermarks#resource) and list of [properties](https://developers.google.com/youtube/v3/docs/watermarks#properties).

| Method | HTTP request | Description |
|---|---|---|
| URIs relative to `https://www.googleapis.com/youtube/v3` |||
| `https://developers.google.com/youtube/v3/docs/watermarks/set` | `POST /watermarks/set` | Uploads a watermark image to YouTube and sets it for a channel. |
| `https://developers.google.com/youtube/v3/docs/watermarks/unset` | `POST /watermarks/unset` | Deletes a channel's watermark image. |)



# Channels: list

**Note:** The `channel` resource's `https://developers.google.com/youtube/v3/docs/channels#statistics.subscriberCount` property value has been updated to reflect a YouTube policy change that affects the way that subscriber counts are displayed. For more information, see [Revision History](https://developers.google.com/youtube/v3/revision_history#release_notes_09_10_2019) or the [YouTube Help Center](https://support.google.com/youtube/answer/6051134). Returns a collection of zero or more `channel` resources that match the request criteria.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 1 unit.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/channels/list#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
GET https://www.googleapis.com/youtube/v3/channels
```

### Authorization

A request that retrieves the `auditDetails` part for a `channel` resource must provide an authorization token that contains the `https://www.googleapis.com/auth/youtubepartner-channel-audit` scope. In addition, any token that uses that scope must be revoked when the MCN decides to accept or reject the channel or within two weeks of the date that the token was issued.

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `part` | `string` The `part` parameter specifies a comma-separated list of one or more `channel` resource properties that the API response will include. If the parameter identifies a property that contains child properties, the child properties will be included in the response. For example, in a `channel` resource, the `contentDetails` property contains other properties, such as the `uploads` properties. As such, if you set `part=contentDetails`, the API response will also contain all of those nested properties. The following list contains the `part` names that you can include in the parameter value: - `auditDetails` - `brandingSettings` - `contentDetails` - `contentOwnerDetails` - `id` - `localizations` - `snippet` - `statistics` - `status` - `topicDetails` |
| **Filters (specify exactly one of the following parameters)** |||
| `categoryId` | `string` This parameter has been deprecated. The `categoryId` parameter specified a [YouTube guide category](https://developers.google.com/youtube/v3/docs/guideCategories) and could be used to request YouTube channels associated with that category. |
| `forHandle` | `string` The `forHandle` parameter specifies a YouTube handle, thereby requesting the channel associated with that handle. The parameter value can be prepended with an `@` symbol. For example, to retrieve the resource for the "Google for Developers" channel, set the `forHandle` parameter value to either `GoogleDevelopers` or `@GoogleDevelopers`. |
| `forUsername` | `string` The `forUsername` parameter specifies a YouTube username, thereby requesting the channel associated with that username. |
| `id` | `string` The `id` parameter specifies a comma-separated list of the YouTube channel ID(s) for the resource(s) that are being retrieved. In a `channel` resource, the `id` property specifies the channel's YouTube channel ID. |
| `managedByMe` | `boolean` This parameter can only be used in a properly [authorized request](https://developers.google.com/youtube/v3/guides/authentication). **Note:** This parameter is intended exclusively for YouTube content partners. Set this parameter's value to `true` to instruct the API to only return channels managed by the content owner that the `onBehalfOfContentOwner` parameter specifies. The user must be authenticated as a CMS account linked to the specified content owner and `onBehalfOfContentOwner` must be provided. |
| `mine` | `boolean` This parameter can only be used in a properly [authorized request](https://developers.google.com/youtube/v3/guides/authentication). Set this parameter's value to `true` to instruct the API to only return channels owned by the authenticated user. |
| **Optional parameters** |||
| `hl` | `string` The `hl` parameter instructs the API to retrieve localized resource metadata for a specific [application language that the YouTube website supports](https://developers.google.com/youtube/v3/docs/i18nLanguages). The parameter value must be a language code included in the list returned by the `https://developers.google.com/youtube/v3/docs/i18nLanguages/list` method. If localized resource details are available in that language, the resource's `snippet.localized` object will contain the localized values. However, if localized details are not available, the `snippet.localized` object will contain resource details in the resource's [default language](https://developers.google.com/youtube/v3/docs/channels#snippet.defaultLanguage). |
| `maxResults` | `unsigned integer` The `maxResults` parameter specifies the maximum number of items that should be returned in the result set. Acceptable values are `0` to `50`, inclusive. The default value is `5`. |
| `onBehalfOfContentOwner` | `string` This parameter can only be used in a properly [authorized request](https://developers.google.com/youtube/v3/guides/authentication). **Note:** This parameter is intended exclusively for YouTube content partners. The `onBehalfOfContentOwner` parameter indicates that the request's authorization credentials identify a YouTube CMS user who is acting on behalf of the content owner specified in the parameter value. This parameter is intended for YouTube content partners that own and manage many different YouTube channels. It allows content owners to authenticate once and get access to all their video and channel data, without having to provide authentication credentials for each individual channel. The CMS account that the user authenticates with must be linked to the specified YouTube content owner. |
| `pageToken` | `string` The `pageToken` parameter identifies a specific page in the result set that should be returned. In an API response, the `nextPageToken` and `prevPageToken` properties identify other pages that could be retrieved. |

### Request body

Do not provide a request body when calling this method.

## Response

If successful, this method returns a response body with the following structure:

```
{
  "kind": "youtube#channelListResponse",
  "etag": etag,
  "nextPageToken": string,
  "prevPageToken": string,
  "pageInfo": {
    "totalResults": integer,
    "resultsPerPage": integer
  },
  "items": [
    channel Resource
  ]
}
```

### Properties

The following table defines the properties that appear in this resource:

| Properties ||
|---|---|
| `kind` | `string` Identifies the API resource's type. The value will be `youtube#channelListResponse`. |
| `etag` | `etag` The Etag of this resource. |
| `nextPageToken` | `string` The token that can be used as the value of the `pageToken` parameter to retrieve the next page in the result set. |
| `prevPageToken` | `string` The token that can be used as the value of the `pageToken` parameter to retrieve the previous page in the result set. Note that this property is not included in the API response if the corresponding API request set the `https://developers.google.com/youtube/v3/docs/channels/list#managedByMe` parameter to `true`. |
| `pageInfo` | `object` The `pageInfo` object encapsulates paging information for the result set. |
| `pageInfo.totalResults` | `integer` The total number of results in the result set. |
| `pageInfo.resultsPerPage` | `integer` The number of results included in the API response. |
| `items[]` | `list` A list of channels that match the request criteria. |

## Errors

The following table identifies error messages that the API could return in response to a call to this method. For more details, see [YouTube Data API - Errors](https://developers.google.com/youtube/v3/docs/errors).

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `invalidCriteria` | A maximum of one of the following filters may be specified:`id`, `categoryId`, `mine`, `managedByMe`, `forHandle`, `forUsername`. In case of content owner authentication via the `onBehalfOfContentOwner` parameter, only the `id` or `managedByMe` may be specified. |
| `forbidden (403)` | `channelForbidden` | The channel specified by the `id` parameter does not support the request or the request is not properly authorized. |
| `notFound (404)` | `categoryNotFound` | The category identified by the `categoryId` parameter cannot be found. Use the [guideCategories.list](https://developers.google.com/youtube/v3/docs/guideCategories/list) method to retrieve a list of valid values. |
| `notFound (404)` | `channelNotFound` | The channel specified in the `id` parameter cannot be found. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.





# Channels: update

The API now supports the ability to mark your [channel](https://developers.google.com/youtube/v3/docs/channels) or [videos](https://developers.google.com/youtube/v3/docs/videos) as "made for kids." In addition, `channel` and `video` resources also now contain a property that identifies the "made for kids" status of that channel or video. The YouTube API Services Terms of Service and Developer Policies were also updated on 10 January 2020. For more information, see the revision histories for the [YouTube Data API Service](https://developers.google.com/youtube/v3/revision_history) and the [YouTube API Services Terms of Service](https://developers.google.com/youtube/terms/revision-history). Updates a channel's metadata. Note that this method only supports updates to the `channel` resource's `brandingSettings`, `invideoPromotion`, and `localizations` objects and their child properties.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 50 units.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/channels/update#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
PUT https://www.googleapis.com/youtube/v3/channels
```

### Authorization

This request requires authorization with at least one of the following scopes. To read more about authentication and authorization, see [Implementing OAuth 2.0 authorization](https://developers.google.com/youtube/v3/guides/authentication).

| Scope |
|---|
| `https://www.googleapis.com/auth/youtubepartner` |
| `https://www.googleapis.com/auth/youtube` |
| `https://www.googleapis.com/auth/youtube.force-ssl` |

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `part` | `string` The `part` parameter serves two purposes in this operation. It identifies the properties that the write operation will set as well as the properties that the API response will include. The API only allows the parameter value to be set to either `brandingSettings`, `invideoPromotion`, or `localizations`. (You can only update any one of those parts with a single request.) Note that this method overrides the existing values for all of the mutable properties that are contained in the part that the parameter value specifies. |
| **Optional parameters** |||
| `onBehalfOfContentOwner` | `string` This parameter can only be used in a properly [authorized request](https://developers.google.com/youtube/v3/guides/authentication). The `onBehalfOfContentOwner` parameter indicates that the authenticated user is acting on behalf of the content owner specified in the parameter value. This parameter is intended for YouTube content partners that own and manage many different YouTube channels. It allows content owners to authenticate once and get access to all their video and channel data, without having to provide authentication credentials for each individual channel. The actual CMS account that the user authenticates with needs to be linked to the specified YouTube content owner. |

### Request body

Provide a `https://developers.google.com/youtube/v3/docs/channels#resource` resource in the request body.
For that resource:

- You must specify a value for these properties:

  - `id`
- You can set values for these properties:

  - `brandingSettings.channel.country`
  - `brandingSettings.channel.description`
  - `brandingSettings.channel.defaultLanguage`
  - `brandingSettings.channel.keywords`
  - `brandingSettings.channel.trackingAnalyticsAccountId`
  - `brandingSettings.channel.unsubscribedTrailer`
  - `localizations.(key)`
  - `localizations.(key).title`
  - `localizations.(key).description`
  - `status.selfDeclaredMadeForKids`

  If you are submitting an update request, and your request does not specify a value for a property that already has a value, the property's existing value will be deleted.

## Response

If successful, this method returns a `https://developers.google.com/youtube/v3/docs/channels#resource` resource in the response body.

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Fore more details, see [YouTube Data API - Errors](https://developers.google.com/youtube/v3/docs/errors).

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `brandingValidationError` | One of the values in the `brandingSettings` object failed validation. Use the `https://developers.google.com/youtube/v3/docs/channels/list` method to retrieve the existing settings for the channel, and update the property values following the guidelines in the `https://developers.google.com/youtube/v3/docs/channels#resource` resource documentation. |
| `badRequest (400)` | `channelTitleUpdateForbidden` | When updating a channel's `brandingSettings part`, you must set the `brandingSettings.channel.title` property's value to the channel's current title or omit the property. The API returns an error if you change the property's value. |
| `badRequest (400)` | `defaultLanguageNotSetError` | The `defaultLanguage` must be set to update `localizations`. |
| `badRequest (400)` | `invalidBrandingOption` | One of the branding settings that you specified does not exist. Use the `https://developers.google.com/youtube/v3/docs/channels/list` method to retrieve valid values and make sure to update them following the guidelines in the `https://developers.google.com/youtube/v3/docs/channels#resource` resource documentation. |
| `badRequest (400)` | `invalidCustomMessage` | The request metadata specifies an invalid custom message. Check the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.items[].customMessage` property in the resource that the request sent. |
| `badRequest (400)` | `invalidDuration` | The request metadata specifies an invalid duration in the invideoPromotion part. |
| `badRequest (400)` | `invalidDuration` | The request metadata specifies an invalid position type for determining how the promoted item is positioned in the video player. Check the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.position.type` property in the resource that the request sent. |
| `badRequest (400)` | `invalidRecentlyUploadedBy` | The request metadata specifies an invalid channel ID. Check the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.items[].id.recentlyUploadedBy` property in the resource that the request sent. |
| `badRequest (400)` | `invalidTimingOffset` | The request metadata specifies an invalid timing offset in the invideoPromotion part. |
| `badRequest (400)` | `invalidTimingOffset` | The request metadata specifies an invalid timing offset for determining when the promoted item should be displayed in the video player. Check the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.timing.offsetMs` property in the resource that the request sent. |
| `badRequest (400)` | `invalidTimingType` | The request metadata specifies an invalid timing method for determining when the promoted item should be displayed in the video player. Check the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.timing.type` property in the resource that the request sent. |
| `badRequest (400)` | `localizationValidationError` | One of the values in the localizations object failed validation. Use the `https://developers.google.com/youtube/v3/docs/channels/list` method to retrieve valid values and make sure to update them following the guidelines in [the channels resource documentation.](https://developers.google.com/youtube/v3/docs/channels#resource) |
| `badRequest (400)` | `tooManyPromotedItems` | Number of allowed promoted items exceeded in the invideoPromotion part. |
| `forbidden (403)` | `channelForbidden` | The channel specified in the `id` parameter does not support the request or the request is not properly authorized. |
| `forbidden (403)` | `promotedVideoNotAllowed` | The channel that the API request is attempting to update cannot be found. Check the value of the `id` property in the `channel` resource that the request sent to ensure that the channel ID is correct. |
| `forbidden (403)` | `websiteLinkNotAllowed` | The specified website URL is not allowed. |
| `notFound (404)` | `channelNotFound` | The channel specified in the `id` parameter cannot be found. |
| `notFound (404)` | `channelNotFound` | The channel specified by the `id` parameter cannot be found or does not have branding options. |
| `notFound (404)` | `unknownChannelId` | The specified channel ID was not found. |
| `notFound (404)` | `unknownChannelId` | The specified recentlyUploadedBy channel ID was not found. |
| `notFound (404)` | `unknownVideoId` | The [video ID](https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.items[].videoId) specified as a promoted item cannot be found. |
| `required (400)` | `requiredItemIdType` | The request metadata must specify an item type in the invideoPromotion part. |
| `required (400)` | `requiredItemId` | The request metadata must specify an item id the invideoPromotion part. |
| `required (400)` | `requiredTimingOffset` | The request metadata must specify a default timing offset so that YouTube can determine when to display the promoted item. Set the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.defaultTiming.offsetMs` property in the resource that the request sends. |
| `required (400)` | `requiredTimingOffset` | The request metadata must specify a timing offset so that YouTube can determine when to display the promoted item. Set the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.timing.offsetMs` property in the resource that the request sends. |
| `required (400)` | `requiredTimingType` | The request metadata must specify a timing method so that YouTube can determine when to display the promoted item. Set the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.defaultTiming.type` property in the resource that the request sends. |
| `required (400)` | `requiredTimingType` | The request metadata must specify a timing method so that YouTube can determine when to display the promoted item. Set the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.timing.type` property in the resource that the request sends. |
| `required (400)` | `requiredTiming` | The request metadata must specify a timing for each item in the `invideoPromotion` part. |
| `required (400)` | `requiredVideoId` | The request metadata must specify a [video ID](https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.items[].videoId) to identify the promoted item. |
| `required (400)` | `requiredWebsiteUrl` | The request metadata must specify a website URL in the invideoPromotion part. Set the value of the `https://developers.google.com/youtube/v3/docs/channels#invideoPromotion.items[].id.websiteUrl` property in the resource that the request sends. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.








# Comments

A `comment` resource contains information about a single YouTube comment. A `comment` resource can represent a comment about either a video or a channel. In addition, the comment could be a top-level comment or a reply to a top-level comment.

## Methods

The API supports the following methods for `comments` resources:

[list](https://developers.google.com/youtube/v3/docs/comments/list)
:   Returns a list of comments that match the API request parameters.
    [Try it now](https://developers.google.com/youtube/v3/docs/comments/list#usage).

[insert](https://developers.google.com/youtube/v3/docs/comments/insert)
:   Creates a reply to an existing comment. **Note:** To create a top-level comment, use the `https://developers.google.com/youtube/v3/docs/commentThreads/insert` method.
    [Try it now](https://developers.google.com/youtube/v3/docs/comments/insert#usage).

[update](https://developers.google.com/youtube/v3/docs/comments/update)
:   Modifies a comment.
    [Try it now](https://developers.google.com/youtube/v3/docs/comments/update#usage).

[delete](https://developers.google.com/youtube/v3/docs/comments/delete)
:   Deletes a comment.
    [Try it now](https://developers.google.com/youtube/v3/docs/comments/delete#usage).

[setModerationStatus](https://developers.google.com/youtube/v3/docs/comments/setModerationStatus)
:   Sets the moderation status of one or more comments. The API request must be authorized by the owner of the channel or video associated with the comments.
    [Try it now](https://developers.google.com/youtube/v3/docs/comments/setModerationStatus#usage).

## Resource representation

The following JSON structure shows the format of a `comments` resource:

```
{
  "kind": "youtube#comment",
  "etag": etag,
  "id": string,
  "snippet": {
    "authorDisplayName": string,
    "authorProfileImageUrl": string,
    "authorChannelUrl": string,
    "authorChannelId": {
      "value": string
    },
    "channelId": string,
    "textDisplay": string,
    "textOriginal": string,
    "parentId": string,
    "canRate": boolean,
    "viewerRating": string,
    "likeCount": unsigned integer,
    "moderationStatus": string,
    "publishedAt": datetime,
    "updatedAt": datetime
  }
}
```

### Properties

The following table defines the properties that appear in this resource:

| Properties ||
|---|---|
| `kind` | `string` Identifies the API resource's type. The value will be `youtube#comment`. |
| `etag` | `etag` The Etag of this resource. |
| `id` | `string` The ID that YouTube uses to uniquely identify the comment. |
| `snippet` | `object` The `snippet` object contains basic details about the comment. |
| `snippet.authorDisplayName` | `string` The display name of the user who posted the comment. |
| `snippet.authorProfileImageUrl` | `string` The URL for the avatar of the user who posted the comment. |
| `snippet.authorChannelUrl` | `string` The URL of the comment author's YouTube channel, if available. |
| `snippet.authorChannelId` | `object` This object encapsulates information about the comment author's YouTube channel, if available. |
| `snippet.authorChannelId.value` | `string` The ID of the comment author's YouTube channel, if available. |
| `snippet.channelId` | `string` The ID of the YouTube channel associated with the comment. |
| `snippet.textDisplay` | `string` The comment's text. The text can be retrieved in either plain text or HTML. (The `comments.list` and `commentThreads.list` methods both support a `textFormat` parameter, which specifies the chosen text format.) Even the plain text may differ from the original comment text. For example, it may replace video links with video titles. |
| `snippet.textOriginal` | `string` The original, raw text of the comment as it was initially posted or last updated. The original text is only returned to the authenticated user if they are the comment's author. |
| `snippet.parentId` | `string` The unique ID of the parent comment. This property is only set if the comment was submitted as a reply to another comment. |
| `snippet.canRate` | `boolean` This setting indicates whether the current viewer can rate the comment. |
| `snippet.viewerRating` | `string` The rating the viewer has given to this comment. This property doesn't identify `dislike` ratings, though this behavior is subject to change. In the meantime, the property value is `like` if the viewer has rated the comment positively. The value is `none` in all other cases, including the user having given the comment a negative rating or not having rated the comment. Valid values for this property are: - `like` - `none` |
| `snippet.likeCount` | `unsigned integer` The total number of likes (positive ratings) the comment has received. |
| `snippet.moderationStatus` | `string` The comment's moderation status. This property is only returned if the API request was authorized by the owner of the channel or the video on which the requested comments were made. Also, this property isn't set if the API request used the `https://developers.google.com/youtube/v3/docs/comments/list#id` filter parameter. Valid values for this property are: - `heldForReview` - `likelySpam` - `published` - `rejected` |
| `snippet.publishedAt` | `datetime` The date and time when the comment was orignally published. The value is specified in [ISO 8601](https://www.w3.org/TR/NOTE-datetime) format. |
| `snippet.updatedAt` | `datetime` The date and time when the comment was last updated. The value is specified in [ISO 8601](https://www.w3.org/TR/NOTE-datetime) format. |


# Comments: list

Returns a list of comments that match the API request parameters.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 1 unit.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/comments/list#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
GET https://www.googleapis.com/youtube/v3/comments
```

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `part` | `string` The `part` parameter specifies a comma-separated list of one or more `comment` resource properties that the API response will include. The following list contains the `part` names that you can include in the parameter value: - `id` - `snippet` |
| **Filters (specify exactly one of the following parameters)** |||
| `id` | `string` The `id` parameter specifies a comma-separated list of comment IDs for the resources that are being retrieved. In a `comment` resource, the `id` property specifies the comment's ID. |
| `parentId` | `string` The `parentId` parameter specifies the ID of the comment for which replies should be retrieved. **Note:** YouTube currently supports replies only for top-level comments. However, replies to replies may be supported in the future. |
| **Optional parameters** |||
| `maxResults` | `unsigned integer` The `maxResults` parameter specifies the maximum number of items that should be returned in the result set. **Note:** This parameter is not supported for use in conjunction with the `https://developers.google.com/youtube/v3/docs/comments/list#id` parameter. Acceptable values are `1` to `100`, inclusive. The default value is `20`. |
| `pageToken` | `string` The `pageToken` parameter identifies a specific page in the result set that should be returned. In an API response, the `nextPageToken` property identifies the next page of the result that can be retrieved. **Note:** This parameter is not supported for use in conjunction with the `https://developers.google.com/youtube/v3/docs/comments/list#id` parameter. |
| `textFormat` | `string` This parameter indicates whether the API should return comments formatted as HTML or as plain text. The default value is `html`. Acceptable values are: - `html` -- Returns the comments in HTML format. This is the default value. - `plainText` -- Returns the comments in plain text format. |

### Request body

Do not provide a request body when calling this method.

## Response

If successful, this method returns a response body with the following structure:

```
{
  "kind": "youtube#commentListResponse",
  "etag": etag,
  "nextPageToken": string,
  "pageInfo": {
    "totalResults": integer,
    "resultsPerPage": integer
  },
  "items": [
    comment Resource
  ]
}
```

### Properties

The following table defines the properties that appear in this resource:

| Properties ||
|---|---|
| `kind` | `string` Identifies the API resource's type. The value will be `youtube#commentListResponse`. |
| `etag` | `etag` The Etag of this resource. |
| `nextPageToken` | `string` The token that can be used as the value of the `pageToken` parameter to retrieve the next page in the result set. |
| `pageInfo` | `object` The `pageInfo` object encapsulates paging information for the result set. |
| `pageInfo.totalResults` | `integer` The total number of results in the result set. |
| `pageInfo.resultsPerPage` | `integer` The number of results included in the API response. |
| `items[]` | `list` A list of comments that match the request criteria. |

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Please see the [error message](https://developers.google.com/youtube/v3/docs/errors) documentation for more detail.

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `operationNotSupported` | The id filter is only compatible with comments based on Google+. |
| `forbidden (403)` | `forbidden` | One or more of the requested comments cannot be retrieved due to insufficient permissions. The request might not be properly authorized. |
| `notFound (404)` | `commentNotFound` | One or more of the specified comments cannot be found. Check the values of the request's `https://developers.google.com/youtube/v3/docs/comments/list#id` and `https://developers.google.com/youtube/v3/docs/comments/list#parentId` parameters to ensure that they are correct. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.



# Comments: insert

Creates a reply to an existing comment. **Note:** To create a top-level comment, use the `https://developers.google.com/youtube/v3/docs/commentThreads/insert` method.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 50 units.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/comments/insert#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
POST https://www.googleapis.com/youtube/v3/comments
```

### Authorization

This request requires authorization with at least one of the following scopes ([read more about authentication and authorization](https://developers.google.com/youtube/v3/guides/authentication)).

| Scope |
|---|
| `https://www.googleapis.com/auth/youtube.force-ssl` |

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `part` | `string` The `part` parameter identifies the properties that the API response will include. Set the parameter value to `snippet`. The `snippet` part has a quota cost of 2 units. The following list contains the `part` names that you can include in the parameter value: - `id` - `snippet` |

### Request body

Provide a [comment resource](https://developers.google.com/youtube/v3/docs/comments#resource) in the request body.
For that resource:

- You must specify a value for these properties:

  <br />

  - `snippet.textOriginal`
  - `snippet.parentId`

  <br />

- You can set values for these properties:

  <br />

  - `snippet.textOriginal`

  <br />

## Response

If successful, this method returns a [comment resource](https://developers.google.com/youtube/v3/docs/comments#resource) in the response body.

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Please see the [error message](https://developers.google.com/youtube/v3/docs/errors) documentation for more detail.

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `commentTextRequired` | The `comment` resource that is being inserted must specify a value for the `snippet.textOriginal` property. Comments cannot be empty. |
| `badRequest (400)` | `commentTextTooLong` | The `comment` resource that is being inserted contains too many characters in the `snippet.textOriginal` property. |
| `badRequest (400)` | `invalidCustomEmoji` | The `comment` resource that is being inserted contains invalid custom emoji. |
| `badRequest (400)` | `invalidCommentMetadata` | The request metadata is invalid. |
| `badRequest (400)` | `operationNotSupported` | The API user is not able to insert a comment in reply to the top-level comment identified by the `snippet.parentId` property. In a `commentThread` resource, the `https://developers.google.com/youtube/v3/docs/commentThreads#snippet.canReply` property indicates whether the current viewer can reply to the thread. |
| `badRequest (400)` | `parentCommentIsPrivate` | The specified parent comment is private. The API does not support replies to private comments. |
| `badRequest (400)` | `parentIdMissing` | The comment that is being inserted must be linked to a parent comment. However, the `comment` resource in the body of the API request did not specify a value for the `https://developers.google.com/youtube/v3/docs/comments#snippet.parentId` property. |
| `badRequest (400)` | `processingFailure` | The API server failed to successfully process the request. While this can be a transient error, it usually indicates that the request's input is invalid. Check the structure of the `comment` resource in the request body to ensure that it is valid. |
| `forbidden (403)` | `forbidden` | The comment cannot be created due to insufficient permissions. The request might not be properly authorized. |
| `forbidden (403)` | `ineligibleAccount` | The YouTube account used to authorize the API request must be merged with the user's Google account to insert a comment or comment thread. |
| `notFound (404)` | `parentCommentNotFound` | The specified parent comment could not be found. Check the value of the `https://developers.google.com/youtube/v3/docs/comments#snippet.parentId` property in the request body to ensure that it is correct. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.

# Comments: update

Modifies a comment.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 50 units.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/comments/update#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
PUT https://www.googleapis.com/youtube/v3/comments
```

### Authorization

This request requires authorization with at least one of the following scopes ([read more about authentication and authorization](https://developers.google.com/youtube/v3/guides/authentication)).

| Scope |
|---|
| `https://www.googleapis.com/auth/youtube.force-ssl` |

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `part` | `string` The `part` parameter identifies the properties that the API response will include. You must at least include the `snippet` part in the parameter value since that part contains all of the properties that the API request can update. The following list contains the `part` names that you can include in the parameter value: - `id` - `snippet` |

### Request body

Provide a [comment resource](https://developers.google.com/youtube/v3/docs/comments#resource) in the request body.
For that resource:

- You can set values for these properties:

  <br />

  - `snippet.textOriginal`

  <br />

## Response

If successful, this method returns a [comment resource](https://developers.google.com/youtube/v3/docs/comments#resource) in the response body.

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Please see the [error message](https://developers.google.com/youtube/v3/docs/errors) documentation for more detail.

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `commentTextTooLong` | The `comment` resource that is being updated contains too many characters in the `snippet.textOriginal` property. |
| `badRequest (400)` | `invalidCommentMetadata` | The request metadata is invalid. |
| `badRequest (400)` | `operationNotSupported` | Only Google+ based comments can be updated. |
| `badRequest (400)` | `processingFailure` | The API server failed to successfully process the request. While this can be a transient error, it usually indicates that the request's input is invalid. Check the structure of the `comment` resource in the request body to ensure that it is valid. |
| `forbidden (403)` | `forbidden` | The comment could not be updated due to insufficient permissions. The request might not be properly authorized. |
| `forbidden (403)` | `ineligibleAccount` | The YouTube account used to authorize the API request must be merged with the user's Google account to update a comment or comment thread. |
| `notFound (404)` | `commentNotFound` | The specified comment could not be found. Check the value of the `https://developers.google.com/youtube/v3/docs/comments#id` property in the request body to ensure that it is correct. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.

# Comments: setModerationStatus

Sets the moderation status of one or more comments. The API request must be authorized by the owner of the channel or video associated with the comments.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 50 units.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/comments/setModerationStatus#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
POST https://www.googleapis.com/youtube/v3/comments/setModerationStatus
```

### Authorization

This request requires authorization with at least one of the following scopes ([read more about authentication and authorization](https://developers.google.com/youtube/v3/guides/authentication)).

| Scope |
|---|
| `https://www.googleapis.com/auth/youtube.force-ssl` |

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `id` | `string` The `id` parameter specifies a comma-separated list of IDs that identify the comments for which you are updating the moderation status. |
| `moderationStatus` | `string` Identifies the new moderation status of the specified comments. Acceptable values are: - `heldForReview` -- Marks a comment as awaiting review by a moderator. - `published` -- Clears a comment for public display. - `rejected` -- Rejects a comment as being unfit for display. This action also effectively hides all replies to the rejected comment. |
| **Optional parameters** |||
| `banAuthor` | `boolean` The `banAuthor` parameter lets you indicate that you want to automatically reject any additional comments written by the comment's author. Set the parameter value to `true` to ban the author. **Note:** This parameter is only valid if the `https://developers.google.com/youtube/v3/docs/comments/setModerationStatus#moderationStatus` parameter is also set to `rejected`. |

### Request body

Do not provide a request body when calling this method.

## Response

If successful, this method returns an HTTP `204` response code (`No Content`).

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Please see the [error message](https://developers.google.com/youtube/v3/docs/errors) documentation for more detail.

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `banWithoutReject` | The `banAuthor` parameter can only be used if the `moderationStatus` parameter value is `rejected`. |
| `badRequest (400)` | `operationNotSupported` | Comments not based on Google+ offer only limited moderation functionality. |
| `badRequest (400)` | `processingFailure` | The API server failed to successfully process the request. While this can be a transient error, it usually indicates that the request's input is invalid. |
| `forbidden (403)` | `forbidden` | The moderation status of one or more comments cannot be set due to insufficient permissions. The request might not be properly authorized. |
| `notFound (404)` | `commentNotFound` | One or more of the comments that the request is trying to update cannot be found. Check the values of the request's `https://developers.google.com/youtube/v3/docs/comments/setModerationStatus#id` parameter to ensure that they are correct. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.


# Comments: delete

Deletes a comment.

**Quota impact:** A call to this method has a [quota cost](https://developers.google.com/youtube/v3/getting-started#quota) of 50 units.

## Common use cases

The list below shows common use cases for this method. Hover over a use case to see its description, or click on a use case to load sample parameter values in the APIs Explorer. You can open the [fullscreen APIs Explorer](https://developers.google.com/youtube/v3/docs/comments/delete#) to see code samples that dynamically update to reflect the parameter values entered in the Explorer.

The table below shows common use cases for this method. You can click on a use case name to load sample parameter values in the APIs Explorer. Or you can see code samples for a use case in the fullscreen APIs Explorer by clicking on the code icon below a use case name. In the fullscreen UI, you can update parameter and property values and the code samples will dynamically update to reflect the values you enter.
This method has one common use case, which is described below. The buttons below the description populate the APIs Explorer with sample values or open the fullscreen APIs Explorer to show code samples that use those values. The code samples also dynamically update if you change the values.

<br />

## Request

### HTTP request

```
DELETE https://www.googleapis.com/youtube/v3/comments
```

### Authorization

This request requires authorization with at least one of the following scopes ([read more about authentication and authorization](https://developers.google.com/youtube/v3/guides/authentication)).

| Scope |
|---|
| `https://www.googleapis.com/auth/youtube.force-ssl` |

### Parameters

The following table lists the parameters that this query supports. All of the parameters listed are query parameters.

| Parameters ||
|---|---|---|
| **Required parameters** |||
| `id` | `string` The `id` parameter specifies the comment ID for the resource that is being deleted. |

### Request body

Do not provide a request body when calling this method.

## Response

If successful, this method returns an HTTP `204` response code (`No Content`).

## Errors

The following table identifies error messages that the API could return in response to a call to this method. Please see the [error message](https://developers.google.com/youtube/v3/docs/errors) documentation for more detail.

| Error type | Error detail | Description |
|---|---|---|
| `badRequest (400)` | `processingFailure` | The API server failed to successfully process the request. While this can be a transient error, it usually indicates that the request's input is invalid. |
| `forbidden (403)` | `forbidden` | The comment could not be deleted because of insufficient permissions. The request might not be properly authorized. |
| `notFound (404)` | `commentNotFound` | The specified comment could not be found. Check the value of the request's `https://developers.google.com/youtube/v3/docs/comments/delete#id` parameter to ensure that it is correct. |

## Try it!

Use the APIs Explorer to call this API and see the API request and response.
