/*
  Función para validar los campos de los FORMS
  Se ha usado la validación de ejemplo de Bootstrap y la plantilla Jekyll-simple-blog como base
  Se han añadido algunas funciones para validar emails, teléfonos y tallas
*/
(() => {
  'use strict';

  // Función para validar formato de correo electrónico
  function validateEmail(email) {
    email = email.trim(); // Elimina los espacios en blanco antes y después del texto
    // Validar formato de correo electrónico según regex
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  // Función para validar formato de número de teléfono
  function validatePhoneNumber(phoneNumber) {
    phoneNumber = phoneNumber.replace(/\s/g, ''); // Elimina todos los espacios en blanco
    // Validar formato de número de teléfono según regex
    const regex = /^\+?[0-9]{1,3}[0-9]{9}$/;
    return regex.test(phoneNumber);
  }

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation');

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {

      // Validar correo electrónico si existe
      const emailInput = form.querySelector('[type="email"]');
      if (emailInput && emailInput.value.trim() !== '') {
        if (!validateEmail(emailInput.value)) {
          emailInput.setCustomValidity('Por favor, introduce un correo electrónico válido.');
        } else {
          emailInput.setCustomValidity('');
        }
      }

      // Validar número de teléfono si existe
      const phoneNumberInput = form.querySelector('[type="tel"]');
      if (phoneNumberInput && phoneNumberInput.value.trim() !== '') {
        if (!validatePhoneNumber(phoneNumberInput.value)) {
          phoneNumberInput.setCustomValidity('Por favor, introduce un número de teléfono válido (con el prefijo del país opcional y sin espacios ni guiones).');
        } else {
          phoneNumberInput.setCustomValidity('');
        }
      }

      /* Validar si se ha marcado talla
       Solo si el modal checkoutModal no está activo porque al añadir el producto al carrito se deselecciona automáticamente para la siguiente compra
       Sin embargo, si se intenta realizar el pedido desde la misma página, como el modal está sobre los detalles, da error porque intenta validar la talla en los detalles del fondo
      */
      const sizeInputs = form.querySelectorAll('[name="size"]');
      // Si existen tallas en el producto y no está activo el modal de pedido
      if (sizeInputs && !document.getElementById('checkoutModal').classList.contains('show')) {
        let sizeSelected = false;

        // Recorre e intenta validar todas las tallas
        sizeInputs.forEach(input => {
          // A cada talla del array, añadir un listener para ocultar el mensaje de validación
          input.addEventListener('change', () => {
            const validationMessage = document.getElementById('validation-for-size');
            // Eliminando d-block no se muestra el div con el mensaje
            validationMessage.classList.remove('d-block');
          });
          // Comprobar si se ha seleccionado alguna talla
          if (input.checked) {
            sizeSelected = true;
          }
        });

        // Verificar si no se ha seleccionado alguna talla y mostrar mensaje de validación
        if (sizeInputs.length > 0 && !sizeSelected) {
          sizeInputs.forEach(input => {
            input.setCustomValidity('Selecciona una talla.');
          });
          $('#validation-for-size').addClass('d-block');
        } else {
          // Eliminar el mensaje de validación si se selecciona una talla
          sizeInputs.forEach(input => {
            input.setCustomValidity('');
          });
          $('#validation-for-size').removeClass('d-block');
        }
      }

      // Evitar el envío si no se ha validado correctamente
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      // Añadir la clase was-validated para validar el formulario
      form.classList.add('was-validated');
    }, false);

    // Agregar un evento 'input' para eliminar el mensaje de error del input que lo contenía
    form.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', () => {
        input.setCustomValidity('');
      });
    });
  });
})();


/*
Funciones para NAVBAR
*/

// Ocultar/mostrar navbar con scroll (from Jekyll-simple-blog)
document.addEventListener('DOMContentLoaded', function () {
  // Show the navbar when the page is scrolled up
  var MQL = 992;

  //primary navigation slide-in effect
  if ($(window).width() > MQL) {
    var headerHeight = $('#mainNav').height();
    $('#mainNav').addClass('is-visible is-fixed');
    $(window).on('scroll', {
      previousTop: 0
    },
      function () {
        var currentTop = $(window).scrollTop();
        //check if user is scrolling up
        if (currentTop < this.previousTop) {
          //if scrolling up...
          if (currentTop > 0 && $('#mainNav').hasClass('is-fixed')) {
            $('#mainNav').addClass('is-visible');
          } else {
            $('#mainNav').removeClass('is-visible is-fixed');
          }
        } else if (currentTop > this.previousTop) {
          //if scrolling down...
          $('#mainNav').removeClass('is-visible');
          if (currentTop > headerHeight && !$('#mainNav').hasClass('is-fixed')) {
            $('#mainNav').addClass('is-fixed');
          }
        }
        this.previousTop = currentTop;
      });
  }
});

// Mostrar botón para ir al inicio de la página
var btnScroll = document.getElementById("btnScrollUp");
if (btnScroll) {
  // Al cargar la página oculta el botón
  btnScroll.style.display = "none";

  // Cuando el usuario hace scroll lo muestra u oculta según se haya desplazado del top
  window.onscroll = function () { scroll() };
  function scroll() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      btnScroll.style.display = "block";
    } else {
      btnScroll.style.display = "none";
    }
  }
}
// When the user clicks on the button, scroll to the top of the document
function scrollToTop() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}



/*
Funciones para VIDEOS
*/

// Mostrar vídeos de manera progresiva (botón "Cargar más")
$(document).ready(function () {
  var btnLoadMore = $('#load-more');

  // Verificar si el botón existe en la página (en el ejemplo que vi usaban .length)
  if (btnLoadMore.length > 0) {
    // Vídeos mostrados al cargar la página
    var videosShown = 2;
    // Nuevos vídeos mostrados al hacer click en el botón
    var videosPerLoad = 2;

    // Ocultar los demás videos al cargar la página
    $('[id^="video_"]:gt(' + (videosShown - 1) + ')').hide();
  }

  // Manejar el clic en el botón "Cargar más"
  $('#load-more').click(function () {
    // Calcular el índice del último video a mostrar
    var videoLastShown = videosShown + videosPerLoad;
    // Mostrar los siguientes videos
    $('[id^="video_"]:lt(' + videoLastShown + ')').show();
    // Actualizar la cantidad de videos mostrados
    videosShown = videoLastShown;

    // Ocultar el botón si ya no hay más videos para mostrar
    if (videosShown >= $('#load-more').data('total-videos')) {
      $('#load-more').hide();
    }
  });
});





/*
  Funciones para MULTIMEDIA
*/

// Pop-up en multimedia que muestra la imagen seleccionada
document.addEventListener('DOMContentLoaded', function () {
  // Obtener las imágenes de la galería y el carusel (comprobar si existe el carusel para evitar mensajes en consola)
  var galleryImages = document.querySelectorAll('.image-gallery img');
  var carousel = document.getElementById('carouselMultimedia');
  if (carousel) {
    carousel = new bootstrap.Carousel(carousel);

    // Generar dinámicamente las imágenes en el carrusel del modal
    // Obtener el elemento interno del carrusel para agregar elementos dinámicamente
    var carouselInner = document.querySelector('#carouselMultimedia .carousel-inner');
    // Recorrer todas las imágenes de la galería
    galleryImages.forEach(function (image, index) {
      // Crear un nuevo elemento de carrusel para cada imagen
      var carouselItem = document.createElement('div');
      carouselItem.classList.add('carousel-item');
      if (index === 0) {
        // Marcar el primer elemento como activo (necesario para que se muestre)
        carouselItem.classList.add('active');
      }

      // Obtener los datos de la imagen en la galería
      var imageSrc = image.getAttribute('src');
      var altText = image.getAttribute('alt');
      var slideTo = image.getAttribute('data-bs-slide-to');
      // Agregar la imagen al elemento del carrusel
      carouselItem.innerHTML = `<img src="${imageSrc}" alt="${altText}" class="d-block" data-bs-slide="${slideTo}">`;
      // Agregar el elemento del carrusel al elemento interno del carrusel
      carouselInner.appendChild(carouselItem);
    });

    // Agregar un listener de clic para cada imagen en la galería
    galleryImages.forEach(function (image, index) {
      image.addEventListener('click', function () {
        var slideTo = parseInt(image.dataset.bsSlideTo);
        carousel.to(slideTo);
      });
    });
  }
});



/*
  Funciones para EVENTOS
*/

// Buscar e iterar por todos los eventos que haya en la página
document.addEventListener("DOMContentLoaded", function () {
  // Obtener todos los tickets de eventos
  var tickets = document.getElementsByClassName('ticket');

  // Si hay tickets, itera por todos y obtiene el texto de la fecha y hora del ticket
  if (tickets) {
    for (var i = 0; i < tickets.length; i++) {
      var dateString = tickets[i].querySelector('.date').textContent.trim();
      // Procesar la fecha y hora
      processDate(tickets[i], dateString);
    }
  }
});

// Función para dividir la fecha y asignarlo a cada campo
function processDate(element, dateString) {
  // Convertir la cadena de fecha en un objeto Date
  var date = parseDate(dateString);

  // Obtener las cadenas de fecha y hora locales
  var dateLocale = date.toLocaleDateString('es-ES', { day: '2-digit', month: 'long' });
  var timeLocale = date.toLocaleTimeString('es-ES', { timeStyle: 'short' });

  // Actualizar el contenido de los tickets con las cadenas de fecha y hora locales
  element.querySelector('.weekday').textContent = date.toLocaleDateString('es-ES', { weekday: 'long' });
  element.querySelector('.day').textContent = dateLocale;
  element.querySelector('.year').textContent = date.getFullYear();
  element.querySelector('.hour').textContent = timeLocale;

  // Aplicar filtro de escala de grises a los elementos pasados (lo comprueba la función)
  grayscaleFilter(element, date);
}

// Función para aplicar un filtro a los eventos pasados
function grayscaleFilter(element, date) {
  // Obtener la fecha y hora actual
  var currentDate = new Date();

  // Comparar con la fecha y hora actual y aplicar el filtro de escala de grises si es anterior
  if (date < currentDate) {
    // Aplicar el filtro de escala de grises y deshabilitar botones
    element.style.filter = 'grayscale(100%) brightness(50%)';
    var buttonElement = element.querySelector('.btn');
    if (buttonElement) {
      buttonElement.classList.add('border-0', 'disabled');
    }
  }
}

// Función para parsear la fecha en formato "31-12-2023 00:00"
function parseDate(dateString) {
  // Dividir la cadena de fecha y hora
  var parts = dateString.split(' ');
  var dateParts = parts[0].split('-');
  var timeParts = parts[1].split(':');

  // Crear un nuevo objeto Date con los componentes separados
  var day = parseInt(dateParts[0], 10);
  var month = parseInt(dateParts[1], 10) - 1; // Los meses en JavaScript son 0-11
  var year = parseInt(dateParts[2], 10);
  var hour = parseInt(timeParts[0], 10) || 0;
  var minute = parseInt(timeParts[1], 10) || 0;

  // Retornar el objeto Date
  return new Date(year, month, day, hour, minute);
}





/*
  Funciones para TIENDA
*/

// Listeners de funciones cuando se carga la página
document.addEventListener('DOMContentLoaded', function () {
  // Itera sobre los elementos del carrito para deshabilitar los botones de reducir cantidad en el carrito
  var itemsInCart = document.querySelectorAll('[id$="_quantity"]');
  itemsInCart.forEach(function (item) {
    var itemId = item.id.replace('_quantity', '');
    disableBtnReduceQuantityinCart(itemId);
  });

  // Deshabilita el botón de reducir cantidad en los detalles del producto
  disableReduceQuantityBtn();
  // Calcula el precio total y subtotal de los productos del carrito
  total();
});

// Función para manejar las opciones del producto
function changeImage(selectedImage, selectedOption) {
  // Actualiza la imagen principal con la nueva imagen seleccionada entre las opciones
  document.getElementById('mainImage').src = selectedImage;

  // Actualiza el modal con la nueva imagen
  document.getElementById('zoomImage').src = selectedImage;

  // Actualiza el valor de los input hidden
  document.getElementById('optionInput').value = selectedOption; // input opcion
  document.getElementById('imageInput').value = selectedImage; // input imagen
}

// Función para mostrar el subtotal de cada producto y calcular el precio total en carritoModal
function total() {
  // Obtener todos los items en el carrito
  var itemsInChart = document.querySelectorAll('[item-cart]');
  var totalSpan = document.getElementById('totalSpan');
  if (itemsInChart && totalSpan) {
    var total = 0;

    // Iterar sobre cada elemento del carrito
    itemsInChart.forEach(function (item) {
      // Obtener los valores del elemento actual
      var quantityInput = item.querySelector('[id$="_quantity"]');
      var priceInput = item.querySelector('[name$="_price"]');
      var subtotalItem = item.querySelector('[id$="_subtotal"]');

      // Verificar los valores de cantidad y precio y pasarlos a número
      if (quantityInput && priceInput) {
        // Parsear cantidad y precio
        var quantity = parseInt(quantityInput.value, 10);
        var price = parseFloat(priceInput.value);

        // Calcular el subtotal de cada producto
        var subtotal = quantity * price;

        // Añadir el subtotal al precio total de la compra
        total += subtotal;

        // Actualizar el precio subtotal del item
        subtotalItem.textContent = subtotal.toFixed(2) + ' €';
      }
    });

    // Actualizar el contenido del div con la suma total
    totalSpan.textContent = total.toFixed(2) + ' €';
    // Actualizar el valor del input oculto
    document.getElementById('totalInput').value = total.toFixed(2);
  }
}


// Función para comprobar y actualizar el estado del botón en base al valor de la cantidad en /carrito
function disableBtnReduceQuantityinCart(itemId) {
  var quantityInput = document.getElementById(itemId + '_quantity');
  var btnReduceQuantity = document.getElementById(itemId + '_btnReduceQuantityinCart');

  // Verifica si el input de cantidad y el botón existen antes de continuar
  if (quantityInput && btnReduceQuantity) {
    // Si la cantidad es 1 (o menos) deshabilitarlo
    if (quantityInput.value <= 1) {
      btnReduceQuantity.disabled = true;
      $('#btnReduceQuantityinCart').addClass('bg-transparent disabled');

      // He añadido esta condición (junto al correpondiente else) para deshabilitar el botón de añadir productos limitándolo a 10
      // Sin embargo, se pueden añadir de 10 en 10, no pone un máximo en el pedido de 10 productos iguales
    } else if (quantityInput.value >= 10) {
      $('#btnIncreaseQuantityinCart').addClass('bg-transparent disabled');

    } else {
      btnReduceQuantity.disabled = false;
      $('#btnIncreaseQuantityinCart').removeClass('bg-transparent disabled');
      $('#btnReduceQuantityinCart').removeClass('bg-transparent disabled');
    }
  }
}


// Función para comprobar y actualizar el estado del botón en base al valor de la cantidad en /detalles
function disableReduceQuantityBtn() {
  var quantityInput = document.getElementById('quantity');
  var btnReduceQuantity = document.getElementById('btnReduceQuantity');
  var btnIncreaseQuantity = document.getElementById('btnIncreaseQuantity');

  if (quantityInput && btnReduceQuantity) {
    // Si la cantidad es 1 (o menos) deshabilitarlo
    if (quantityInput.value <= 1) {
      btnReduceQuantity.disabled = true;
      $('#btnReduceQuantity').addClass('bg-transparent disabled');

      // He añadido esta condición (junto al correpondiente else) para deshabilitar el botón de añadir productos limitándolo a 10
      // Sin embargo, se pueden añadir de 10 en 10, no pone un máximo en el pedido de 10 productos iguales
    } else if (quantityInput.value >= 10) {
      $('#btnIncreaseQuantity').addClass('bg-transparent disabled');

    } else {
      btnReduceQuantity.disabled = false;
      $('#btnIncreaseQuantity').removeClass('bg-transparent disabled');
      $('#btnReduceQuantity').removeClass('bg-transparent disabled');
    }
  }
}



// Función para modificar la cantidad en /detalles
function modifyQuantity(operation) {
  var quantityInput = document.getElementById('quantity');
  var currentValue = parseInt(quantityInput.value);

  // Actualiza el valor según la operación del parámetro
  if (operation === 'increase') {
    quantityInput.value = currentValue + 1;
  } else if (operation === 'reduce' && currentValue > 1) {
    quantityInput.value = currentValue - 1;
  }
  // Actualizar estado del botón
  disableReduceQuantityBtn();
}



// Función para actualizar la cantidad de productos en #checkoutModal
function updateQuantity(itemId, operation) {
  var quantityInput = document.getElementById(itemId + '_quantity');

  var currentValue = parseInt(quantityInput.value, 10);
  // Variables necesarias para crear la clave del producto
  var product = document.getElementById(itemId + '_item').value;
  var option = document.getElementById(itemId + '_option')?.value ?? null;
  var size = document.getElementById(itemId + '_size')?.value ?? null;

  // Actualiza el valor según la operación del parámetro
  if (operation === 'increase') {
    quantityInput.value = currentValue + 1;
  } else if (operation === 'reduce' && currentValue > 1) {
    quantityInput.value = currentValue - 1;
  }

  // Actualizar estado del botón
  disableBtnReduceQuantityinCart(itemId);

  // Realizar una petición AJAX para actualizar la cantidad del producto en $_SESSION
  fetch('update-item-quantity', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      // Datos necesarios para que el Controlador pueda crear una clave única del producto y operar sobre él
      itemId: itemId,
      updatedQuantity: quantityInput.value,

      product: product,
      option: option,
      size: size,
    }),
  })
    .then(response => {
      // Verificar si la respuesta es exitosa
      if (response.ok) {
        console.log('Cantidad actualizada con éxito en el servidor.');
        // Crear la cadena de texto para el log
        var logText = "itemId_" + itemId + ": " + product;
        logText += option ? "-" + option : "";
        logText += size ? "-" + size : "";
        logText += " => " + quantityInput.value;
        console.log(logText);

      } else {
        // Manejar la respuesta de error que contiene HTML
        return response.text().then(errorMessage => {
          console.error('Error en la solicitud POST:', errorMessage);
        });
      }
    })
    .catch(error => {
      console.error('Error en la solicitud POST:', error);
    });
  total();
}

// Función para eliminar productos
function deleteItem(itemId) {
  var selectedItem = document.getElementById(itemId + '_delete');
  // Variables necesarias para crear la clave del producto
  var product = document.getElementById(itemId + '_item').value;
  var option = document.getElementById(itemId + '_option')?.value ?? null;
  var size = document.getElementById(itemId + '_size')?.value ?? null;

  if (selectedItem) {
    // Buscar el item seleccionado y eliminarlo de la vista
    var itemToDelete = document.getElementById('item_' + itemId);
    if (itemToDelete) {
      itemToDelete.remove();
    }
  }

  // Hacer la petición AJAX para eliminar el elemento de $_SESSION
  fetch("delete-item", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      // Datos necesarios para que el Controlador pueda crear una clave única del producto y operar sobre él
      itemId: itemId,

      product: product,
      option: option,
      size: size,
    }),
  })
    .then(response => {
      // Verificar si la respuesta es exitosa
      if (response.ok) {
        console.log('Item eliminado con éxito en el servidor.');
        // Crear la cadena de texto para el log
        var logText = "itemId_" + itemId + ": " + product;
        logText += option ? "-" + option : "";
        logText += size ? "-" + size : "";
        console.log(logText);

      } else {
        // Manejar la respuesta de error que contiene HTML
        return response.text().then(errorMessage => {
          console.error('Error en la solicitud POST:', errorMessage);
        });
      }
    })
    .catch(error => {
      console.error('Error en la solicitud POST:', error);
    });

  total();
}


// Mostrar pop-up en vez de $response con el POST /add-to-cart
/* Tuve problemas con este fetch por lo que puse condicionales de comprobación de más.
  Luego vi que este código no era lo que me da error, pero no reducí los condicionales por si liaba algo
*/
document.addEventListener('DOMContentLoaded', function () {
  var cartForm = document.getElementById('cartForm');
  var cartModal = document.getElementById('cartConfirmation');
  // Si el elemento existe, crea el modal 
  if (cartForm) {
    if (cartModal) {
      var cartConfirmation = bootstrap.Modal.getOrCreateInstance(cartModal);
    }

    cartForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Evitar el envío del formulario por defecto

      // Realizar petición AJAX
      if (cartForm.checkValidity()) {
        var formData = new FormData(cartForm);
        fetch("add-to-cart", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(responseText => {
            // Mostrar el modal de confirmación
            cartConfirmation.show();
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        // Si el formulario no es válido, mostrar los mensajes de error de validación
        cartForm.classList.add('was-validated');
      }
    });

    // Recargar la página al cerrar el modal de confirmación
    cartConfirmation._element.addEventListener('hidden.bs.modal', function () {
      window.location.reload();
    });
  }
});

// Mostrar pop-up en vez de $response con el POST /checkout
document.addEventListener('DOMContentLoaded', function () {
  var checkoutForm = document.getElementById('checkoutForm');
  var checkoutConfirmation = document.getElementById('checkoutConfirmation');
  var checkoutModal = document.getElementById('checkoutModal');

  if (checkoutConfirmation) {
    checkoutConfirmation = new bootstrap.Modal(checkoutConfirmation);
  }

  if (checkoutModal) {
    checkoutModal = new bootstrap.Modal(checkoutModal);

    checkoutForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Evitar el envío del formulario por defecto

      // Verificar si el formulario es válido
      if (checkoutForm.checkValidity()) {
        // Realizar petición AJAX para enviar el formulario con la compra
        var formData = new FormData(checkoutForm);
        fetch("send-checkout", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(responseText => {
            // Mostrar el modal de confirmación solo después de una respuesta exitosa
            if (checkoutConfirmation) {
              checkoutConfirmation.show();
            }
            if (checkoutModal) {
              checkoutModal.hide();
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        // Si el formulario no es válido, mostrar los mensajes de error de validación
        checkoutForm.classList.add('was-validated');
      }
    });

    // Recargar página cuando se cierra el modal de confirmación
    if (checkoutConfirmation) {
      checkoutConfirmation._element.addEventListener('hidden.bs.modal', function () {
        window.location.reload();
      });
    }
  }
});

// Recargar página cuando se cierra el modal del pedido (actualiza la badge del botón del carrito)
document.addEventListener('DOMContentLoaded', function () {
  var checkoutModal = document.getElementById('checkoutModal');

  if (checkoutModal) {
    checkoutModal = new bootstrap.Modal(checkoutModal);

    checkoutModal._element.addEventListener('hidden.bs.modal', function () {
      window.location.reload();
    });
  }
});



/*
Funciones para CONTACTO y la newsletter
*/
// Mostrar pop-up en vez de $response con el POST /newsletter
document.addEventListener('DOMContentLoaded', function () {
  // Formulario de newsletter en /inicio
  var newsletterForm = document.getElementById('newsletterForm');
  // Formulario de newsletter en el footer
  var nlFooterForm = document.getElementById('newsletterFooterForm');
  var newsletterConfirmation = document.getElementById('newsletterConfirmation');

  if (newsletterConfirmation) {
    newsletterConfirmation = new bootstrap.Modal(newsletterConfirmation);
  }

  // Para la newsletter en inicio
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Evitar el envío del formulario por defecto

      if (newsletterForm.checkValidity()) {
        var formData = new FormData(newsletterForm);
        // Realizar petición AJAX para enviar el formulario con la suscripción a la newsletter en /inicio
        fetch("send-newsletter", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(responseText => {
            if (newsletterConfirmation) {
              newsletterConfirmation.show();
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        newsletterForm.classList.add('was-validated');
      }
    });
  }

  // Para la newsletter en el footer
  if (nlFooterForm) {
    nlFooterForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Evitar el envío del formulario por defecto

      if (nlFooterForm.checkValidity()) {
        var formData = new FormData(nlFooterForm);
        // Realizar petición AJAX para enviar el formulario con la suscripción a la newsletter en el footer
        fetch("send-newsletter", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(responseText => {
            if (newsletterConfirmation) {
              newsletterConfirmation.show();
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        nlFooterForm.classList.add('was-validated');
      }
    });
  }

  // Recargar la página al cerrar el modal de confirmación
  if (newsletterConfirmation) {
    newsletterConfirmation._element.addEventListener('hidden.bs.modal', function () {
      window.location.reload();
    });
  }
});


// Mostrar pop-up en vez de $response con el POST /send-contact
document.addEventListener('DOMContentLoaded', function () {
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    var contactConfirmation = document.querySelector('#contactConfirmation');
    // Si el elemento existe, intenta obtener o crear la instancia del modal
    if (contactConfirmation) {
      var contactConfirmation = bootstrap.Modal.getOrCreateInstance(contactConfirmation);
    }

    contactForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Evitar el envío del formulario por defecto
      // Verificar si el formulario es válido
      if (contactForm.checkValidity()) {

        // Realizar petición AJAX para enviar el formulario de contacto
        var formData = new FormData(contactForm);
        fetch("send-contact", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(responseText => {
            // Si la respuesta del servidor es exitosa, mostrar el modal de confirmación
            if (contactConfirmation) {
              contactConfirmation.show();
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        // Si el formulario no es válido, mostrar los mensajes de error de validación
        contactForm.classList.add('was-validated');
      }
    });

    // Recargar la página cuando se oculta el modal de confirmación
    contactConfirmation._element.addEventListener('hidden.bs.modal', function () {
      window.location.reload();
    });
  }
});

