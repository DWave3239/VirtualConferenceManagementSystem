    {if="isset($delay)"}
      <h5 class="delayMessage" style="width: 100%; text-align: center; padding: 5px;"><div>Delay for this session will be {$delay}.</div></h5>
    {/if}
    <script src='https://8x8.vc/external_api.js' async></script>
    <div id="jaas-container"></div>
    <script type="text/javascript">
      const jaas_domain = "8x8.vc";
      const jaas_options = {
        roomName: "vpaas-magic-cookie-253dbc50398b4685819dc7167cedbb79/{$room}",
        height: "100%",
        parentNode: document.querySelector("#jaas-container"),
        jwt: "{$jwt}"
      };

      window.onload = () => {
        document.getElementById('jaas-container').parentElement.style.cssText = 'padding: 0 !important; margin: 0;';

        var nav = document.querySelector('.navbar');
        var del = document.querySelector('#content');
        
        var height = nav.offsetHeight;
        height += del.offsetHeight;
        height += parseInt(window.getComputedStyle(nav).getPropertyValue('margin-top'));
        height += parseInt(window.getComputedStyle(nav).getPropertyValue('margin-bottom'));
        height += parseInt(window.getComputedStyle(del).getPropertyValue('margin-top'));
        height += parseInt(window.getComputedStyle(del).getPropertyValue('margin-bottom'));

        document.getElementById('jaas-container').style.cssText = 'height: calc(100vh - '+height+'px)';

        const api = new JitsiMeetExternalAPI(jaas_domain, jaas_options);
      }
    </script>