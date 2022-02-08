{if="!$logged_in"}
    To fully access this page please log in.
{else}
    <p>Hello <b>{$full_name}</b>!</p>
    {if="$auth_level >= $auth_level_program_chair"}
    <br />
    <div class="card">
        <div class="card-header" id="backendHeader" data-toggle="collapse" data-target="#backendCard" aria-expanded="true" aria-controls="backendCard">
            Administrative features
        </div>
        <div id="backendCard" class="collapse show" aria-labelledby="backendHeader" data-parent="#backendCard">
            {include="../cruds/index"}
        </div>
    </div>
    {/if}
    {if="$auth_level >= $auth_level_presenter"}
    <br />
    <div class="card">
        <div class="card-body">
            {if="(isset($userSegments['chair_in']) && !empty($userSegments['chair_in'])) || (isset($userSegments['presenter_in']) && !empty($userSegments['presenter_in']))"}
            {if="isset($userSegments['chair_in']) && !empty($userSegments['chair_in'])"}
            <p>
                <p>Your will be chair in the following session(s):</p>
                <div class="flex-container">
                    <table class="table table-sm">
                        <thead>
                            <th>Segment</th>
                            <th>Time</th>
                        </thead>
                        <tbody>
                            {loop="$userSegments['chair_in']"}
                                <tr>
                                    <td><a href="{$_watch_}/{$value.roomLink}">{$value.name}</a></td>
                                    <td>{$value.date}, {$value.time_from} - {$value.time_until}</td>
                                </tr>
                            {/loop}
                        </tbody>
                    </table>
                </div>
            </p>
            {/if}
            {if="isset($userSegments['presenter_in']) && !empty($userSegments['presenter_in'])"}
            <p>
                <p>Your are presenting in the following session(s):</p>
                <div class="flex-container">
                    <table class="table table-sm">
                        <thead>
                            <th>Segment</th>
                            <th>Time</th>
                            <th>Paper</th>
                        </thead>
                        <tbody>
                        {loop="$userSegments['presenter_in']"}
                            <tr>
                                <td><a href="{$_watch_}/{$value.roomLink}">{$value.segment_name}</a></td>
                                <td>{$value.date}, {$value.time_from} - {$value.time_until}</td>
                                <td>{$value.paper_name} by {$value.authorList}</td>
                            </tr>
                        {/loop}
                        </tbody>
                    </table>
                </div>
            </p>
            {/if}
            {else}
                You have no assigned segments.
            {/if}
        </div>
    </div>
    {/if}
    <br />
    <div class="card">
        <div class="card-body">
            <p class="small text-center">
                <small>
                    Communication channels may also include:
                    <a href="https://developer.rocket.chat/guides/developer/embedded-layout" target="_blank">RocketChat (embedded, Widget?)</a> or 
                    <a href="https://slack.com/intl/de-de/help/articles/360035092414-Leute-aus-anderen-Unternehmen-in-einen-Channel-einladen" target="_blank">Slack (share channel for external use)</a> or 
                    <a href="https://jku.zoom.us/j/7945050299" target="_blank">special rooms in meeting software</a>
                </small>
                <p style="text-align: center;"><a class="external-link" href="{$_watch_}/MockupSocialsRoomForTalks"><b>Social Room</b></a></p>
            </p>

            <p>
                <div id="yourUpcomingEvents">
                    <div class="card">
                        <div class="card-header" id="yourUpcomingEventsHeader" data-toggle="collapse" data-target="#yourUpcomingEventsCard" aria-expanded="true" aria-controls="yourUpcomingEventsCard">
                            Your upcoming events
                        </div>
                        <div id="yourUpcomingEventsCard" class="collapse show" aria-labelledby="yourUpcomingEventsHeader" data-parent="#yourUpcomingEvents">
                            <div class="card-body">
                                {include="../scheduleDynamic"}
                            </div>
                        </div>
                    </div>
                </div>
            </p>
        </div>
    </div>
    <hr>
    <div class="container">
        <h4><a href="https://jitsi.github.io/handbook/docs/devops-guide/devops-guide-quickstart">Jitsi Self-Hosting Guide</a></h4>
        <h4><a href="https://www.8x8.com/products/apis/video">How to embedd Jitsi (or 8x8) video streams</a></h4>
    </div>
{/if}