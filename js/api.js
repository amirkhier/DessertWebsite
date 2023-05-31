const API_KEY = "b69677ec67294cc6bb16962449619368";
const searchForm = document.querySelector("form");
const searchInput = document.querySelector("#ingredients");
const recipeList = document.querySelector(".recipe-card");
const recipeDetails = document.querySelector("#recipe-details");
const calorieFilter = document.querySelector("#calories");
const sugarFilter = document.querySelector("#sugar");
let appear = document.getElementById("etitle");
const dessertIngredients = ['Flour', 'Sugar', 'Butter', 'Eggs', 'Milk', 'Vanilla extract', 'Baking powder', 'Chocolate chips', 'Strawberries', 'Whipped cream', 'Nuts', 'Cocoa powder', 'Condensed milk', 'Graham cracker crumbs', 'Lemon zest', 'Cream cheese', 'Raspberries', 'Cinnamon', 'Caramel sauce', 'Pecans', 'Coconut flakes', 'Oreo cookies', 'Peanut butter', 'Blueberries', 'Almond extract'];
const randomIndex = Math.floor(Math.random() * dessertIngredients.length);


searchForm.addEventListener("submit", e => {
  e.preventDefault();
  appear.style.display = "block";
  const query = searchInput.value.trim().split(",").join("+");
  let url = `https://api.spoonacular.com/recipes/complexSearch?apiKey=${API_KEY}&query=${query}&type=dessert`;

 if (calorieFilter.value !== "" && sugarFilter.value !== "") {
    const calorieValue = calorieFilter.value || "";
    const sugarValue = sugarFilter.value || "";
    url += `&maxCalories=${calorieValue}&maxSugar=${sugarValue}`;
  } else if (calorieFilter.value !== "") {
    const calorieValue = calorieFilter.value || "";
    url += `&maxCalories=${calorieValue}`;
  } else if (sugarFilter.value !== "") {
    const sugarValue = sugarFilter.value || "";
    url += `&maxSugar=${sugarValue}`;
  } 

  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data.results.length === 0) {
        recipeList.innerHTML = "<p>No recipes found,"+"We Suggest you to try to type ingridient such as : "+dessertIngredients[randomIndex]+","+dessertIngredients[randomIndex-1]+"."+ "<br> ,Or try to Change the sugar/calories amount</p>";
       
      } else {
        displayRecipes(data.results);
        
      }
    })
    .catch(error => {
      console.log(error);
    });
});


function displayRecipes(recipes) {
  recipeList.innerHTML = "";
  recipes.forEach(recipe => {
    var recipeURL = "https://spoonacular.com/recipes/" + recipe.title.split(" ").join("-") + "-" + recipe.id;

    const recipeCard = `
      <div class="col-md-4">
        <div class="card">
          <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
            <img src="${recipe.image}" class="img-fluid" alt="image">
          </div>
          <div class="card-body pb-0">
            <div class="d-flex justify-content-between">
              <div>
                <p>
                  <a href="${recipeURL}" role="button" class="main-name main-color recipe-click">${recipe.title}</a>
                </p>
              </div>
            </div>
          </div>
          <hr class="my-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
              <p class="text-dark fw-bold">By: Spoonacular</p>
            </div>
          </div>
        </div>
      </div>
    `;
    recipeList.insertAdjacentHTML("beforeend", recipeCard);
  });
}

recipeList.addEventListener("click", e => {
  if (e.target.matches("h5")) {
    const recipeId = e.target.parentNode.nextElementSibling.dataset.id;
    fetch(`https://api.spoonacular.com/recipes/${recipeId}/information?apiKey=${API_KEY}`)
      .then(response => response.json())
      .then(data => {
        displayRecipeDetails(data);
      })
      .catch(error => {
        console.log(error);
      });
  }
});

calorieFilter.addEventListener("change", () => {
  searchForm.dispatchEvent(new Event("submit"));
});

sugarFilter.addEventListener("change", () => {
  searchForm.dispatchEvent(new Event("submit"));
});
