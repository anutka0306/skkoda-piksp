// Не япония: 
// land rover, Jaguar, Porsche, Audi, Bentley, Lamborghini, Seat, Skoda, Volkswagen
// Япония: 
// Lexus, Infiniti, Nissan, Toyota, Mazda, Mitsubishi

$(document).ready(function(){
	//Скрыть PopUp при загрузке страницы
	//PopUpHide();
	//PopUp2Hide();
	
	var path = location.pathname.split('/')[1]; // let
	// console.log(path);

	/*if (path.length > 0 && path != 'blog' && path != 'offers' && path != 'contacts'){
		$('.markN1').remove();
		$('.markN2').remove();
		$('.markN3').remove();
		$('.addressN1').remove();
		$('.addressN2').remove();
		$('.addressN3').remove();
	}*/

	let vor = [
		'Lexus',
		'Infinity',
	];

	$('#markN1').on('change', function() {
		if (vor.indexOf( this.value ) != -1 ) {
			$('#addressN2_К20').attr("disabled", true);
			$('#addressN2_К20').removeAttr('selected');
			$('#addressN2_СПБ4').attr("disabled", true);
			$('#addressN2_СПБ4').removeAttr("selected");

			$('#addressN2_В2АЕ').removeAttr('disabled');
			$('#addressN2_В2АЕ').attr('selected', 'true');
		} else {
			$('#addressN2_В2АЕ').attr("disabled", true);
			$('#addressN2_В2АЕ').removeAttr("selected");

			$('#addressN2_К20').removeAttr('disabled');
			$('#addressN2_К20').attr('selected', 'true');
			$('#addressN2_СПБ4').removeAttr('disabled');
		}
	});
	$('#markN2').on('change', function() {
		if (vor.indexOf( this.value ) != -1 ) {
			$('#addressN2_К20').attr("disabled", true);
			$('#addressN2_К20').removeAttr('selected');
			$('#addressN2_СПБ4').attr("disabled", true);
			$('#addressN2_СПБ4').removeAttr("selected");

			$('#addressN2_В2АЕ').removeAttr('disabled');
			$('#addressN2_В2АЕ').attr('selected', 'true');
		} else {
			$('#addressN2_В2АЕ').attr("disabled", true);
			$('#addressN2_В2АЕ').removeAttr("selected");

			$('#addressN2_К20').removeAttr('disabled');
			$('#addressN2_К20').attr('selected', 'true');
			$('#addressN2_СПБ4').removeAttr('disabled');
		}
	});
	/*$('#markN3').on('change', function() {

		if(vor.indexOf( this.value ) != -1 ) {
			$('#addressN3_К20').attr("disabled", true);
			$('#addressN3_К20').removeAttr('selected');

			$('#addressN3_В2АЕ').removeAttr('disabled');
			$('#addressN3_В2АЕ').attr('selected', 'true');

		} else {
			$('#addressN3_В2АЕ').attr("disabled", true);
			$('#addressN3_В2АЕ').removeAttr("selected");

			$('#addressN3_К20').removeAttr('disabled');
			$('#addressN3_К20').attr('selected', 'true');

		}
	});*/

    // Форма "Свяжитесь с нами"
	$(".contact-send-button").click(function (e){
		e.preventDefault();

		let phone = $('.phoneN1-popup').val();
		let url = $('#urlN1').val();
		let mark = '';
		let address = '';
		let b_control = $('#b_control').val();

		if (path.length == 0 || path == 'blog' || path == 'offers' || path == 'contacts') {
			mark = $('#markN1').val();
			address = $('#addressN1').val();
		} else {
			mark = path;
			if (path == 'bmw' || path == 'mercedes') {
				// console.log('bmw - mercedes');
				address = $('#addressN1').val(); // address = "Б116";
			} else {
				if (path == 'lexus' || path == 'infinity') {
					// console.log('lexus - infinity');
					address = "В2АЕ";
				} else {
					// console.log('все остальное');
					//address = "K20";
					address = $('#addressN1').val();
				}
			}
		}

		console.log(phone);
		console.log(mark);
		console.log(address);
		
		if ($('.phoneN1-popup').val() == '') {
			alert('Заполните поле телефон');
			return;
		}
		if ($('.phoneN1-popup').val().length < 17) {
			alert('Неверный формат номер телефона');
			return;
		}
		
		$.ajax({
			url:     "/callback_form",
			type:     "POST", //метод отправки
			dataType: "html", //формат данных

			data: {
				'form' : 'Свяжитесь с нами',
				'phone' : phone,
				'url': url,
				'mark' : mark,
				'address' : address,
				'b_control': b_control,
			},
			success: function(response) { //Данные отправлены успешно
				//$('.callback-popup-body').html('<p style="text-align: center; font-weight: 700; padding: 20px;">Спасибо! Ваша заявка отправлена.</p>');
				console.log(response);
			},
			error: function(response) { // Данные не отправлены
				console.log('error');
			}
		});

		ResultShow();
	});
	
	// Форма "Записаться на СТО"
	$(".sto-send-button").click(function (e){
		e.preventDefault();

		let phone = $('.phoneN2-popup').val();
		let url  = $('#urlN2').val();
		let mark = '';
		let address = '';
		let b_control = $('#b_control').val();

		if (path.length == 0 || path == 'blog' || path == 'offers' || path == 'contacts') {
			mark = $('#markN2').val();
			address = $('#addressN2').val();
		} else {
			mark = path;
			if (path == 'bmw' || path == 'mercedes') {
				// console.log('bmw - mercedes');
				address = $('#addressN2').val(); // address = "Б116";
			} else {
				if (path == 'lexus' || path == 'infinity') {
					// console.log('lexus - infinity');
					address = "В2АЕ";
				} else {
					// console.log('все остальное');
					//address = "K20";
					address = $('#addressN2').val();
				}
			}
		}

		console.log(phone);
		console.log(mark);
		console.log(address);
		if ($('.phoneN2-popup').val() == ''){
			alert('Заполните поле телефон');
			return;
		}
		if ($('.phoneN2-popup').val().length < 17){
			alert('Неверный формат номер телефона');
			return;
		}

		$.ajax({
			url: "/callback_form",
			type: "POST", //метод отправки
			dataType: "html", //формат данных

			data: {
				'form' : 'Записаться на СТО',
				'phone' : phone,
				'url' : url,
				'mark' : mark,
				'address' : address,
				'b_control': b_control,
			},
			success: function(response) { //Данные отправлены успешно
				//$('.callback-popup-body').html('<p style="text-align: center; font-weight: 700; padding: 20px;">Спасибо! Ваша заявка отправлена.</p>');
				console.log(response);
			},
			error: function(response) { // Данные не отправлены
				console.log('error');
			}
		});

		//PopUpHide();
		ResultShow();
	});

    // Форма "Заказать Звонок"
	$(".popup-send-button").click(function(e) {
		e.preventDefault();


		let phone = $('.phoneN3-popup').val();
		let url = $('#urlN3').val();
		let mark = '';
		let address = '';

		if (path.length == 0 || path == 'blog' || path == 'offers' || path == 'contacts') {
			mark = $('#markN3').val();
			address = $('#addressN3').val();
		} else {
			mark = $('#markN3').val();
			address = $('#addressN3').val();
			/*mark = path;
			if (path == 'bmw' || path == 'mercedes') {
				// console.log('bmw - mercedes');
				address = "Б116";
			} else {
				if (path == 'lexus' || path == 'infinity') {
					// console.log('lexus - infinity');
					address = "В2АЕ";
				} else {
					// console.log('все остальное');
					address = "K20";
				}
			}*/
		}

		console.log(phone);
		console.log(mark); // Другое - по умолчанию
		console.log(address);
		if($('.phoneN3-popup').val() == ''){
			alert('Заполните поле телефон');
			return;
		}
		if($('.phoneN3-popup').val().length < 17){
			alert('Неверный формат номер телефона');
			return;
		}

		$.ajax({
			url: "/callback_form",
			type: "POST", //метод отправки
			dataType: "html", //формат данных

			data: {
				'form' : 'Заказать Звонок',
				'phone' : phone,
				'url': url,
				'mark' : mark,
				'address' : address,
			},
			success: function(response) { //Данные отправлены успешно
				//$('.callback-popup-body').html('<p style="text-align: center; font-weight: 700; padding: 20px;">Спасибо! Ваша заявка отправлена.</p>');
				 console.log(response);
				ym(75371857,'reachGoal','app_form_feedback');
				return true;
			},
			error: function(response) { // Данные не отправлены
				console.log('error');
			}
		});


	   PopUpHide();
	   ResultShow();
	});
	$(".popup-send-button4").click(function (e){
		e.preventDefault();
		let address = '';
		if($('.phoneN4-popup').val() == ''){
			alert('Заполните поле телефон');
			return;
		}
		if($('.phoneN4-popup').val().length < 17){
			alert('Неверный формат номер телефона');
			return;
		}
		let phone = $('.phoneN4-popup').val();
		let url = $('#urlN4').val();
		address = $('#addressN4').val();
		$.ajax({
			url:     "/callback_form",
			type:     "POST", //метод отправки
			dataType: "html", //формат данных

			data: {
				'phone' : phone,
				'url': url,
				'address' : address,
			},
			success: function(response) { //Данные отправлены успешно
				//$('.callback-popup-body').html('<p style="text-align: center; font-weight: 700; padding: 20px;">Спасибо! Ваша заявка отправлена.</p>');
				console.log(response);
			},
			error: function(response) { // Данные не отправлены
				console.log('error');
			}
		});


		//PopUp2Hide();
		ResultShow();
	});
});

document.addEventListener("DOMContentLoaded", () => {
    let sp_forms = document.querySelectorAll('.save_page_form');
    console.log(sp_forms);
    if(sp_forms !== null){
        for(var i=0; i < sp_forms.length; i++){
            sp_forms[i].addEventListener('submit', function(e){
                e.preventDefault();
                let form = this;
                let phone = form.querySelector('[name="phone"]');
                if(phone.value == ''){
                    alert('Заполните поле телефон');
                    return;
                }
                let url = "/callback_form";
        		let mark = '';
        		let address = form.querySelector('[name="address"]');
				/*let b_control = form.querySelector('[name="b_control"]');*/
				let b_control_value = form.querySelector('[name="b_control"]')?.value || '';
        		let f_text = "Не спешите уходить";
                fetch(url, {
                    method: 'POST',
                    headers: {
                      "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                    },  
                    /*body: 'form='+f_text+'&phone='+phone.value+'&mark='+mark+'&address='+address.value+'&b_control='+b_control.value,*/
                    body: `form=${encodeURIComponent(f_text)}&phone=${encodeURIComponent(phone.value)}&mark=${encodeURIComponent(mark)}&address=${encodeURIComponent(address.value)}&b_control=${encodeURIComponent(b_control_value)}`,

                })
                .then((response) => response.text())
        		.then((data) => {
        		    //console.log("data", data);
        		    let status = form.querySelector('.__status');
        		    
        		    status.innerHTML = '<span class="good_mes">Ваша заявка принята. Наши менеджеры скоро свяжуться с Вами.</span>';
        		    setTimeout(function(){
        		        status.innerHTML = '';
        		        form.reset();
        		    }, 10000);
        		});
				ResultShow();
            })
        }
    }
});

$('select').on('change', function(e) {
	var path = location.pathname.split('/')[1];
	
	var optionSelected = $("option:selected", this);
	var valueSelected = this.value.toLowerCase();
	
	if (valueSelected === 'porsche' || valueSelected === 'land rover' || valueSelected === 'jaguar') {
		$('#addressN3_В2АЕ').attr("disabled", true);
		$('#addressN3_В2АЕ').removeAttr("selected");
		$('#addressN2_СПБ4').attr("disabled", true);
		$('#addressN2_СПБ4').removeAttr("selected");
		$('#addressN4_СПБ4').attr("disabled", true);
        $('#addressN4_СПБ4').removeAttr("selected");
		
		$('#addressN3_К20').removeAttr('disabled');
		$('#addressN3_К20').attr('selected', 'true');
		$('#addressN4_К20').removeAttr('disabled');
        $('#addressN4_К20').attr('selected', 'true');
	} else if (valueSelected === 'lexus' || valueSelected === 'infinity' || valueSelected === 'nissan' ||
				valueSelected === 'toyota' || valueSelected === 'mazda' || valueSelected === 'mitsubishi') {
		$('#addressN3_К20').attr("disabled", true);
		$('#addressN3_К20').removeAttr('selected');
		$('#addressN2_СПБ4').attr("disabled", true);
		$('#addressN2_СПБ4').removeAttr("selected");
		$('#addressN4_К20').removeAttr('disabled');
        $('#addressN4_К20').attr('selected', 'true');
		$('#addressN3_В2АЕ').removeAttr('disabled');
		$('#addressN3_В2АЕ').attr('selected', 'true');
		$('#addressN4_В2АЕ').removeAttr('disabled');
        $('#addressN4_В2АЕ').attr('selected', 'true');
	} else {
		// Б116
		$('#addressN3_В2АЕ').removeAttr('disabled');
		$('#addressN3_К20').removeAttr('disabled');
		$('#addressN4_В2АЕ').removeAttr('disabled');
        $('#addressN4_К20').removeAttr('disabled');
		$('#addressN2_СПБ4').removeAttr('disabled');
	}
});


//Функция отображения PopUp
function PopUpShow(){
	$("#popup1").show();
}
//Функция скрытия PopUp
function PopUpHide(){
	$("#popup1").hide();
}

function PopUp2Show(){
    $("#popup4").show();
}
// Функция скрытия PopUp
function PopUp2Hide(){
    $("#popup4").hide();
}

function ResultShow(){
	$("#popup2").show();
}
function ResultHide(){
	$("#popup2").hide();
}