var currentRound;
var secrets;

async function loginFunc() {
  await import("./creds.js")
    .then(response => {
        secrets = {...response.creds};
    });
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
      document.getElementById("redditlogin").innerHTML = `<a href="https://www.reddit.com/api/v1/authorize?client_id=${secrets.client_id}&response_type=code&state=${random_string}&redirect_uri=http://127.0.0.1:5500/finish_login.html&duration=permanent&scope=identity,edit,flair,submit">Log in with Reddit</a>`;
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
    document.getElementById("currentroundtitle").innerHTML = `<a href="https://reddit.com/${id}" target="_blank"><b>${title}</b></a> by <a target="_blank" href="https://picturega.me/dashboard?player=${hostName}"><b>u/${hostName}</b></a>`;
    document.getElementById("currentroundimage").src = postUrl;
    }
  catch(err) {
    console.error(err)
  }

}

async function logOut() {
  const credentials = btoa(`${secrets.client_id}:${secrets.client_secret}`);
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
  window.location.replace("index.html");
}

async function submitComment() {
  await fetch('https://oauth.reddit.com/api/comment', { 
    method: "POST",
    headers: {
      "User-Agent": "PGSS/1.0 (by u/SpikyLlama)",
      "Content-Type": "application/x-www-form-urlencoded",
      "Authorization": `Bearer ${localStorage.getItem("access_token")}`
    },
    //body: `thing_id=t3_${currentRound.round.id}&text=${document.getElementById("commentbox").value}&api_type=json`
    body: `thing_id=t3_17n14ne&text=${document.getElementById("commentbox").value}&api_type=json`
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