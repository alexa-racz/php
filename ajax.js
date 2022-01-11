const button = document.querySelector("#more");
const matcList = document.querySelector("#matchlist");

button.addEventListener("click", handleUpdate);

async function handleUpdate() {
    let resp = await fetch("ajax.php");
    let data = await resp.json();
    console.log(data);
    // data.then(match => {
    //     let li = document.createElement("li");
    //     li.innerHTML = match['dated'] + match['home']['name'] + " - " + match['away']['name'] + " : " + match['home']['score'] + " - " + match['away']['score'];
    //     matcList.appendChild(li);
    // });
    data.then(x => {
        for (match of x.resp) {
            let li = document.createElement("li");
            li.innerHTML = match['dated'] + match['home']['name'] + " - " + match['away']['name'] + " : " + match['home']['score'] + " - " + match['away']['score'];
            matcList.appendChild(li);
        }
    });
}