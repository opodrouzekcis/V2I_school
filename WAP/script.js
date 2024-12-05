function fetchMushrooms(category = '') {
    const gallery = document.getElementById('gallery');
    gallery.innerHTML = ''; // vyprázdnění galerie v případě přepnutí kategorie

    // zde si načtu data podle kategorií z databáze ( respektive .PHP )
    fetch(`fetch_mushrooms.php?category=${category}`)
        .then(response => response.json()) //odpověď z databáze - formát JSON
        .then(data => {
            data.forEach(mushroom => { //foreach který projde každou položku zvlášť
                const item = document.createElement('div'); //vytvoření objektu HTML
                item.classList.add('gallery-item'); //vytvoření objektu HTML - galerie


                //přidá popis, jméno obrázek
                item.innerHTML = ` 
                    <img src="${mushroom.image_url}" alt="${mushroom.name}">
                    <h3>${mushroom.name}</h3>
                    <p>${mushroom.description}</p>
                `;

                gallery.appendChild(item);
            });
        })

        // v případě chyby vypiš chybu

        .catch(error => {
            console.error('Chyba při výpisu:', error);
        });
}

// Načtení všech údajů hub z databáze po načtení stránky
fetchMushrooms();
