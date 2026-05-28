async function loadStats() {
    try {
        const response = await fetch('../api/stats.php');
        const data = await response.json();
        
        console.log("Statistiques :", data);
    } catch (error) {
        console.error("Erreur lors du chargement des stats :", error);
    }
}

setInterval(loadStats, 5000);
loadStats();