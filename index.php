<html>
  <head>
      <title>PGSS</title>
      <link rel="stylesheet" href="style.css"/>
      <meta name="referrer" content="no-referrer">
    </head>
    <header>
      <div id="userinfo">
        <img id="infosnoo" src="" /><p id="infousername">&nbsp;</p>&nbsp;
        <a href="javascript:logOut()" id="infologout">| Log out</a>
        <a href="javascript:populateCurrentRound()" id="inforefresh"> | Refresh </a>
      </div>
      <div id="redditlogin">
      </div>
    </header>
    <body onload="populateCurrentRound()">
      <div id="currentround">
        <p id="currentroundtitle"></p>
        <img id="currentroundimage" src=""/>
        <p id="currentroundtimer"></p>       
      </div>
      <div id="commentlist">
        <textarea id="commentbox" value="value"></textarea>
        <br><button id="submitcomment" onclick="submitComment()">Submit answer</button>
      </div>
      <script>
        var currentRound;
        var secrets;

        async function loginFunc() {
            if (localStorage.getItem("access_token")) {
              var code; 
              const res = await fetch('https://oauth.reddit.com/api/v1/me', { 
                method: "GET",
                headers: {
                  Authorization: `bearer ${localStorage.getItem("access_token")}`
                }
              });
              const result = await res.json();
              document.getElementById("infosnoo").src = result.snoovatar_img;
              document.getElementById("infousername").innerHTML += result.name;
            } else {
              document.getElementById("userinfo").style.display = "none";
              const random_string = "test";
              document.getElementById("redditlogin").innerHTML = `<a href="https://www.reddit.com/api/v1/authorize?client_id=MzRgMlET7I_0RUGyxfxEgA&response_type=code&state=${random_string}&redirect_uri=https://spikyllama.net/pgss/finish_login.php&duration=permanent&scope=identity,edit,flair,submit">Log in with Reddit</a>`;
            };
        }; 
        loginFunc();

        async function populateCurrentRound() {
          try {
            data = await fetch("https://api.picturega.me/current");
            currentRound = await data.json();
            const title = currentRound.round.title;
            const hostName = currentRound.round.hostName;
            const postUrl = currentRound.round.postUrl;
            const id = currentRound.round.id;
            if (currentRound.winTime) {
              document.getElementById("currentroundtitle").innerHTML = `The most recent round was solved ${unixTimeDifference(currentRound.plusCorrectTime, Date.now() / 1000)} ago.`;
              document.getElementById("currentroundimage").src = postUrl;
            } else {
              document.getElementById("currentroundtitle").innerHTML = `<a href="https://reddit.com/${id}" target="_blank"><b>${title}</b></a> by <a target="_blank" href="https://picturega.me/dashboard?player=${hostName}"><b>u/${hostName}</b></a>`;
              document.getElementById("currentroundimage").src = postUrl;
            }
            if ((Date.now() / 1000 - localStorage.getItem("last_refreshed")) > 3000) {
              const credentials = btoa(`<?php echo "MzRgMlET7I_0RUGyxfxEgA" ?>:<?php echo "CLIENT_SECRET" ?>`);
              const form = new URLSearchParams({
                grant_type: "refresh_token",
                refresh_token: localStorage.getItem("refresh_token"),
              });
              const res = await fetch('https://www.reddit.com/api/v1/access_token', { 
                method: "POST",
                headers: {
                  Authorization: `Basic ${credentials}`
                },
                body: form
              });
              const result = await res.json();
              localStorage.setItem("access_token", result.access_token);
              localStorage.setItem("last_refreshed", Date.now() / 1000);
            }
          } catch(err) {
            console.error(err)
          }
        }

        async function logOut() {
          const credentials = btoa(`<?php echo "MzRgMlET7I_0RUGyxfxEgA" ?>:<?php echo "CLIENT_SECRET" ?>`);
          await fetch('https://www.reddit.com/api/v1/revoke_token', { 
            method: "POST",
            headers: {
              Authorization: `Basic ${credentials}`
            },
            body: `token=${localStorage.getItem("access_token")}&token_type_hint=access_token`
          });
          await fetch('https://www.reddit.com/api/v1/revoke_token', { 
            method: "POST",
            headers: {
              Authorization: `Basic ${credentials}`
            },
            body: `token=${localStorage.getItem("refresh_token")}&token_type_hint=refresh_token`
          });
          localStorage.removeItem("access_token");
          localStorage.removeItem("refresh_token");
          window.location.replace("index.php");
        }

        async function submitComment() {
          await fetch('https://oauth.reddit.com/api/comment', { 
            method: "POST",
            headers: {
              "User-Agent": "PGSS/1.0 (by u/SpikyLlama)",
              "Content-Type": "application/x-www-form-urlencoded",
              "Authorization": `Bearer ${localStorage.getItem("access_token")}`
            },
            body: `thing_id=t3_${currentRound.round.id}&text=${document.getElementById("commentbox").value}&api_type=json`
          }).then(response => {
            if (response.ok) {
              console.log('Comment posted successfully.');
            } else {
              console.error('Failed to post comment. Status:', response.status);
              return response.text();
            }
          });
          document.getElementById("commentbox").value = "";
        }

        function unixTimeDifference(first, second) {
          const diff = first - second;
          const mins = Math.floor(diff / 60);
          const secs = diff - mins * 60;
          return `${mins} minutes and ${secs} seconds`;
        }
      </script>
    </body>
</html>