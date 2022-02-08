<!-- bootstrap -->
<div {if="isset($customSchedulerId)"}id="{$customSchedulerId}"{else}id="mockup_schedule2_full_dynamic"{/if} class="schedule full dynamic">
    {if="isset($customSchedulerId)"}
    <script>
        createScheduler("{$customSchedulerId}", true);
    </script>
    {/if}
    <div class="timezone chooser container">
        Timezone offset: 
        <select name="timezone_offset" class="form-control timezone-offset">
        <option value="-720">(GMT -12:00) Eniwetok, Kwajalein</option>
        <option value="-660">(GMT -11:00) Midway Island, Samoa</option>
        <option value="-600">(GMT -10:00) Hawaii</option>
        <option value="-570">(GMT -9:30) Taiohae</option>
        <option value="-540">(GMT -9:00) Alaska</option>
        <option value="-480">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
        <option value="-420">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
        <option value="-360">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
        <option value="-300">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
        <option value="-270">(GMT -4:30) Caracas</option>
        <option value="-240">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
        <option value="-210">(GMT -3:30) Newfoundland</option>
        <option value="-180">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
        <option value="-120">(GMT -2:00) Mid-Atlantic</option>
        <option value= "-60">(GMT -1:00) Azores, Cape Verde Islands</option>
        <option value=   "0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
        <option value=  "60">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
        <option value= "120">(GMT +2:00) Kaliningrad, South Africa</option>
        <option value= "180">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
        <option value= "210">(GMT +3:30) Tehran</option>
        <option value= "240">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
        <option value= "270">(GMT +4:30) Kabul</option>
        <option value= "300">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
        <option value= "330">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
        <option value= "345">(GMT +5:45) Kathmandu, Pokhara</option>
        <option value= "360">(GMT +6:00) Almaty, Dhaka, Colombo</option>
        <option value= "390">(GMT +6:30) Yangon, Mandalay</option>
        <option value= "420">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
        <option value= "480">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
        <option value= "525">(GMT +8:45) Eucla</option>
        <option value= "540">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
        <option value= "570">(GMT +9:30) Adelaide, Darwin</option>
        <option value= "600">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
        <option value= "630">(GMT +10:30) Lord Howe Island</option>
        <option value= "660">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
        <option value= "690">(GMT +11:30) Norfolk Island</option>
        <option value= "720">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
        <option value= "765">(GMT +12:45) Chatham Islands</option>
        <option value= "780">(GMT +13:00) Apia, Nukualofa</option>
        <option value= "840">(GMT +14:00) Line Islands, Tokelau</option>
        </select>
    </div>

    {loop="$schedule.procedure"}
    <div id="scheduleDay{$counter+1}" class="day table-responsive" data-date="{$key1}">
        <div class="card-header" id="headerDayTable{$counter+1}">
            <div data-toggle="collapse" data-target="#dayTable{$counter+1}" aria-expanded="true" aria-controls="dayTable{$counter+1}">
                <h3>
                    Day <span class="day_index">{$counter1+1}</span> - <span class="day_date">{$key1}</span>
                </h3>
            </div>
        </div>
        <div id="dayTable{$counter+1}" class="collapse {if="!$collapsed"}show{/if}" aria-labelledby="hedaerDayTable{$counter+1}" data-parent="#scheduleDay{$counter+1}">
            <table class="table">
                <thead class="sticky-header">
                    <tr class="table-dark text-dark">
                        <td style="width: 120px;">Time</td>
                        {loop="$value1.tracks"}
                            {loop="$schedule.tracks"}
                                {if="$value3.trackId == $value2"}
                                    <td>
                                        {if="$logged_in"}
                                            <a class="external-link" href="{$_watch_}/{$value3.link}"><b>{$value3.name}</b></a>
                                        {else}
                                            <b>{$value3.name}</b>
                                        {/if}
                                    </td>
                                {/if}
                            {/loop}    
                        {/loop}
                    </tr>
                </thead>
                <tbody>
                    {loop="$value1.slots"}
                    {$track_count=count($value1.tracks)}
                    {$slot_columns=0}
                    {$show_paper_buttons=0}
                    {loop="value2.content"}
                        {$slot_columns=$slot_columns+$value3.colspan}
                        {if="isset($value3['papers']) && count($value3.papers) > 0"}
                            {$show_paper_buttons=1}
                        {/if}
                    {/loop}
                    <tr data-timefrom="{$value2.time.from}" data-timeuntil="{$value2.time.until}">
                        <td rowspan="{$slot_columns/$track_count}" class="timecolumn">
                            <span class="time_from">{if="$print"}{$value2.time.from|substr:0,5}{/if}</span> - <span class="time_until">{if="$print"}{$value2.time.until|substr:0,5}{/if}</span>
                            {if="!$print && $show_paper_buttons"}
                            <div class="show-more">
                                <button class="btn btn-light btn-sm" onclick="openNextMore(this)">Show papers</button>
                            </div>
                            <div class="show-less hide">
                                <button class="btn btn-light btn-sm" onclick="showLess(this)">Show less</button></div>
                            </div>
                            {/if}
                        </td>
                        {$col_sum=0}
                        {loop="$value2.content"}
                            {if="$col_sum == $track_count"}
                        </tr>
                                {$col_sum=0}
                        <tr>
                            {/if}
                        <td colspan="{$value3.colspan}" {if="isset($value3['segment']) && isset($value3.segment.delay) && !empty($value3.segment.delay)"}data-delay="{$value3.segment.delay}"{/if}>
                            {if="isset($value3['segment'])"}
                                {if="is_null($value3.segment.chairId)"}
                                    <b><i>{$value3.segment.name}</i></b>
                                    {if="$auth_level >= $auth_level_program_chair"}
                                        &nbsp;<a href="{$_base_}backend/segments/edit/{$value.segment.segmentId}"><i class="fas fa-pen"></i></a>
                                    {/if}
                                    <br>
                                    {if="isset($value3.segment.delay) && !empty($value3.segment.delay)"}
                                        <em style="color:red;">Delay for this session will be {$value3.segment.delay}.<br>Please be aware that this might effect following sessions.</em><br>
                                    {/if}
                                {else}
                                    {if="$value3.colspan > 1"}
                                        {if="!is_null($value3.segment.individual_link)"}
                                            {if="$logged_in"}
                                                <a class="external-link" href="{$_watch_}/{$value3.segment.individual_link}"><b>{$value3.segment.name}</b></a>
                                                {if="$auth_level >= $auth_level_program_chair"}
                                                    &nbsp;<a href="{$_base_}backend/segments/edit/{$value.segment.segmentId}"><i class="fas fa-pen"></i></a>
                                                {/if}
                                                <br>
                                                {if="isset($value3.segment.delay) && !empty($value3.segment.delay)"}
                                                <em style="color:red;">Delay for this session will be {$value3.segment.delay}.<br><small>Please be aware that this might effect following sessions.</small></em><br>
                                                {/if}                                        
                                            {/if}
                                            <b><small>Session Chair: {$value3.segment.chairName}</small></b><br>
                                            {loop="$value3.papers"}
                                            <p>
                                                <b>{$value4.name}</b><br>
                                                <b>{$value4.authorList}</b><br>
                                                {$value4.country}
                                            </p>
                                            {/loop}
                                        {else}
                                            <b>{$value3.segment.name}</b>
                                        {/if}
                                    {else}
                                        <div class="part-title">
                                            {if="$logged_in"}
                                                <a class="external-link" href="{$_watch_}/{$value3.link}"><b>{$value3.segment.name}</b></a>
                                                {if="$auth_level >= $auth_level_program_chair"}
                                                    &nbsp;<a href="{$_base_}backend/schedule/segments/{$value.segment.segmentId}"><i class="fas fa-pen"></i></a>
                                                {/if}
                                            {else}
                                                <b>{$value3.segment.name}</b>
                                            {/if}
                                        </div>
                                        <br>
                                        <hr>
                                        <div>
                                            {$value3.segment.subtitle}<br>
                                            Session Chair: {$value3.segment.chairName}
                                        </div>
                                        <hr>
                                        <div class="more hide">
                                            {loop="$value3.papers"}
                                            <div class="paper-entry">
                                                <b>{$value4.name}</b><br>
                                                {$value4.authorList}<br>
                                                <small><i>{$value4.country}</i></small>
                                            </div>
                                            {/loop}
                                        </div>
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                        {$col_sum=$col_sum+$value3.colspan}
                        {/loop}
                    <tr>
                    {/loop}
                </tbody>
            </table>
        </div>
    </div>
    {/loop}
</div>