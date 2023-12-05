var mock = true;
var displayedData = [];

function renderObj(obj) {
    const container = document.getElementById("container");
    const item = document.createElement("div");
    const itemThemes = ["bv-1","bv-2","bv-3"];
    const itemTheme = itemThemes[Math.floor(Math.random() * itemThemes.length)];
    item.classList.add("b-item");
    item.classList.add(itemTheme);
    item.setAttribute("data-id", obj.id);

    const badge = document.createElement("span");
    badge.classList.add("b-badge");
    badge.innerHTML = obj.qty

    item.appendChild(badge);
    item.insertAdjacentHTML("beforeend", obj.nama)

    container.appendChild(item);
}

function renderObjects(data) {
    const displayedDataIds = displayedData.map(obj => obj.id_produk);
    data.forEach(e => {
        if (displayedDataIds.indexOf(e.id_produk) === -1) {
            renderObj(e);
        }
    });
    return data;
}

function getData(date, category) {
    let fetchedData = [];
    if (mock) {
        fetchedData = getMockData();
    }
    displayedData = renderObjects(fetchedData);
}

var mockDataSpan = 1;
function getMockData() {
    mockDataSpan = Math.ceil(Math.random() * 3) + mockDataSpan;
    let daMock = theMockData().rows;
    daMock = daMock.sort((a, b) => a.nama.localeCompare(b.nama))
    return daMock.slice(0, Math.min(mockDataSpan, daMock.length));
}

function theMockData() {
    return {
        "table": "UnknownTable",
        "rows":
        [
            {
                "tanggal": "2019-09-01",
                "qty": 215,
                "nama": "Country White 600gr",
                "id_produk": 10,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 7,
                "nama": "Dark Rye 800gr",
                "id_produk": 14,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 105,
                "nama": "English Muffin 90gr",
                "id_produk": 15,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Country White 1200gr",
                "id_produk": 16,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 24,
                "nama": "Country White 1200gr (Sliced Tebal)",
                "id_produk": 17,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 74,
                "nama": "Country White 800gr (Sliced Tebal)",
                "id_produk": 18,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 99,
                "nama": "Country White 600gr (Sliced Tebal)",
                "id_produk": 19,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 14,
                "nama": "Country White 400gr",
                "id_produk": 21,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 12,
                "nama": "Dark Rye 800gr (Sliced Tebal)",
                "id_produk": 22,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 16,
                "nama": "Dark Rye 600gr",
                "id_produk": 23,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 19,
                "nama": "Dark Rye 600gr (Sliced Tebal)",
                "id_produk": 24,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Dark Rye 1200gr (Sliced Tebal)",
                "id_produk": 25,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Dark Rye 1200gr",
                "id_produk": 26,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Classic Charcoal 600gr",
                "id_produk": 27,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 6,
                "nama": "Country Wheat 600gr",
                "id_produk": 28,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 17,
                "nama": "Country Wheat 600gr (Sliced Tebal)",
                "id_produk": 29,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 21,
                "nama": "Seeded Loaf 600gr",
                "id_produk": 31,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Seeded Loaf 600gr (Sliced Tebal)",
                "id_produk": 32,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Quinoa Sourdough 600gr",
                "id_produk": 34,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 35,
                "nama": "Mini Sourdough Baguette 140gr",
                "id_produk": 37,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 5,
                "nama": "Mini Charcoal Baguette 140gr",
                "id_produk": 38,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 35,
                "nama": "Banh Mi Baguette 140gr",
                "id_produk": 39,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Banh Mi Baguette 130gr",
                "id_produk": 42,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 50,
                "nama": "Banh Mi Baguette 125gr",
                "id_produk": 43,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 80,
                "nama": "Potato Bun 60gr (White sesame seed)",
                "id_produk": 44,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 240,
                "nama": "Potato Buns 80gr",
                "id_produk": 45,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 610,
                "nama": "Milk Bun 80gr (Mix Sesame Seeds)",
                "id_produk": 46,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Milk Bun 70gr (Mix Sesame Seeds)",
                "id_produk": 48,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Mini Milk Bun 40gr (White sesame seed)",
                "id_produk": 49,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 35,
                "nama": "Charcoal Milk Bun 80gr",
                "id_produk": 52,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 115,
                "nama": "Brioche Buns 80gr",
                "id_produk": 54,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 150,
                "nama": "Mini Brioche Buns 20gr",
                "id_produk": 55,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 26,
                "nama": "Mini Brioche Buns 40gr",
                "id_produk": 56,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 120,
                "nama": "English Muffin 60gr",
                "id_produk": 58,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 80,
                "nama": "English Muffin 80gr",
                "id_produk": 59,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 30,
                "nama": "Bagel Sesame Seeds 100gr",
                "id_produk": 62,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 45,
                "nama": "Black Sesame Bagel 100gr",
                "id_produk": 64,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Focaccia Rosemary 100gr",
                "id_produk": 65,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Pain de Campagne 400gr",
                "id_produk": 66,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 60,
                "nama": "Ciabatta 100gr",
                "id_produk": 69,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 5,
                "nama": "Ciabatta 130gr",
                "id_produk": 70,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 5,
                "nama": "Milk Hotdog Buns 65gr",
                "id_produk": 72,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 12,
                "nama": "Wholemeal Sandwich Bread (Sliced)",
                "id_produk": 75,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "White Sandwich Bread (Sliced)",
                "id_produk": 76,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Charcoal Bagel Sesame Seeds 100gr",
                "id_produk": 77,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Charcoal Hotdog Buns 65gr",
                "id_produk": 78,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 42,
                "nama": "Pain Au Chocolat 80gr",
                "id_produk": 82,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Cinnamon Roll 80gr",
                "id_produk": 83,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 1,
                "nama": "Banana Chocolate Danish 180gr",
                "id_produk": 86,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 122,
                "nama": "Sourdough Buns 80gr",
                "id_produk": 87,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 37,
                "nama": "Almond Croissant 90gr",
                "id_produk": 89,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 1,
                "nama": "Dark Rye 1000gr",
                "id_produk": 92,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 169,
                "nama": "Plain Croissant 90gr",
                "id_produk": 93,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 68,
                "nama": "Plain Croissant 40gr",
                "id_produk": 94,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Sourdough Baguette 400gr (Medium)",
                "id_produk": 98,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 15,
                "nama": "Sourdough Baguette 400gr (Long)",
                "id_produk": 99,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 125,
                "nama": "Charcoal Buns 80gr",
                "id_produk": 101,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 31,
                "nama": "Brioche Tin Loaf 350gr",
                "id_produk": 102,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 14,
                "nama": "Pain Au Raisin 80gr",
                "id_produk": 103,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 55,
                "nama": "Pain Au Raisin 40gr",
                "id_produk": 107,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Country White 1000gr",
                "id_produk": 117,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 4,
                "nama": "Seeded Loaf 800gr",
                "id_produk": 118,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 4,
                "nama": "Peach Danish 80gr",
                "id_produk": 120,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 5,
                "nama": "Milk Bun 80gr",
                "id_produk": 127,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 100,
                "nama": "Milk Buns 90gr",
                "id_produk": 129,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 16,
                "nama": "Mini Charcoal Buns 40gr",
                "id_produk": 133,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Country White 800gr",
                "id_produk": 134,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Country Wheat 1200gr (Sliced Tebal)",
                "id_produk": 136,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 50,
                "nama": "Pita Pocket 100gr (16 cm)",
                "id_produk": 138,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 4,
                "nama": "Choco Almond Croissant 80gr",
                "id_produk": 139,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 5,
                "nama": "Pita Pocket 130gr (20cm)",
                "id_produk": 162,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Mini Sourdough Baguette 120gr",
                "id_produk": 163,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Turkish Bread 600gr",
                "id_produk": 167,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 4,
                "nama": "Blueberry Danish 80gr",
                "id_produk": 203,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Plain Bagel with Honey 100gr",
                "id_produk": 204,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 4,
                "nama": "Country Wheat 800gr (Sliced Tebal)",
                "id_produk": 208,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Charcoal Buns 100gr",
                "id_produk": 211,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Wholemeal Sandwich Bread (Whole)",
                "id_produk": 214,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Vegan Beetroot Buns 80gr (White Sesame Seeds)",
                "id_produk": 223,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Vegan Spinach Buns 80gr",
                "id_produk": 224,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 50,
                "nama": "Garlic Bagel 100gr",
                "id_produk": 226,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Seeded Loaf 800gr (Sliced Tebal)",
                "id_produk": 227,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 2,
                "nama": "Brownie Fudge (Slab)",
                "id_produk": 248,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Nutella Tart Small",
                "id_produk": 260,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 6,
                "nama": "Brownie Fudge (Sliced)",
                "id_produk": 272,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Pain de Campagne 800gr (Sliced Tebal)",
                "id_produk": 275,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 200,
                "nama": "Brioche Buns 70gr",
                "id_produk": 277,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 8,
                "nama": "Cocoa Lemon Sourdough 1000gr",
                "id_produk": 284,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 20,
                "nama": "Brioche Buns Lobster Roll 100gr",
                "id_produk": 285,
                "id_kategori": null,
                "kategori": null
            },
            {
                "tanggal": "2019-09-01",
                "qty": 10,
                "nama": "Vegan Plain Croissant 70gr",
                "id_produk": 286,
                "id_kategori": null,
                "kategori": null
            }
        ]
    }
}