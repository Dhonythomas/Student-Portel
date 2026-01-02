(async function (){
    const response = await fetch('counter.php');
    const data = await response.json();
    document.getElementById('studentCount').textContent = data.Scount;
    document.getElementById('officeCount').textContent = data.Ocount;
})();