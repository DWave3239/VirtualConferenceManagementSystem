<p class="special_background">
    <i>
        This is a mockup project for my bachelor thesis.<br>
        There is no gain in manipulating anything here.
    </i>
</p>

<img src="{$assets_url}images/og_image.jpg" height="200"><br>

You are currently logged {if="$logged_in"}in{else}out{/if}!
{if="$logged_in"}
<p>Welcome {$userdata.username}!</p>
{/if}