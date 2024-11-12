console.clear();

function CountdownTracker(label, value) {
    var el = document.createElement("span");

    el.className = "flip-clock__piece";
    el.innerHTML =
        '<b class="flip-clock__card card"><b class="card__top"></b><b class="card__bottom"></b><b class="card__back"><b class="card__bottom"></b></b></b>' +
        '<span class="flip-clock__slot">' +
        label +
        "</span>";

    this.el = el;

    var top = el.querySelector(".card__top"),
        bottom = el.querySelector(".card__bottom"),
        back = el.querySelector(".card__back"),
        backBottom = el.querySelector(".card__back .card__bottom");

    this.update = function (val) {
        val = ("0" + val).slice(-2);
        if (val !== this.currentValue) {
            if (this.currentValue >= 0) {
                back.setAttribute("data-value", this.currentValue);
                bottom.setAttribute("data-value", this.currentValue);
            }
            this.currentValue = val;
            top.innerText = this.currentValue;
            backBottom.setAttribute("data-value", this.currentValue);

            this.el.classList.remove("flip");
            void this.el.offsetWidth;
            this.el.classList.add("flip");
        }
    };

    this.update(value);
}

// Calculation adapted from https://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/

function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    return {
        Total: t,
        Days: Math.floor(t / (1000 * 60 * 60 * 24)),
        Hours: Math.floor((t / (1000 * 60 * 60)) % 24),
        Minutes: Math.floor((t / 1000 / 60) % 60),
        Seconds: Math.floor((t / 1000) % 60),
    };
}

function getTime() {
    var t = new Date();
    return {
        Total: t,
        Hours: t.getHours() % 12,
        Minutes: t.getMinutes(),
        Seconds: t.getSeconds(),
    };
}

function Clock(countdown, callback) {
    countdown = countdown ? new Date(Date.parse(countdown)) : false;
    callback = callback || function () {};

    var updateFn = countdown ? getTimeRemaining : getTime;

    this.el = document.createElement("div");
    this.el.className = "flip-clock";

    var trackers = {},
        t = updateFn(countdown),
        key,
        timeinterval;

    for (key in t) {
        if (key === "Total") {
            continue;
        }
        trackers[key] = new CountdownTracker(key, t[key]);
        this.el.appendChild(trackers[key].el);
    }

    var i = 0;
    function updateClock() {
        timeinterval = requestAnimationFrame(updateClock);

        // throttle so it's not constantly updating the time.
        if (i++ % 10) {
            return;
        }

        var t = updateFn(countdown);
        if (t.Total < 0) {
            cancelAnimationFrame(timeinterval);
            for (key in trackers) {
                trackers[key].update(0);
            }
            callback();
            return;
        }

        for (key in trackers) {
            trackers[key].update(t[key]);
        }
    }

    setTimeout(updateClock, 500);
}

let opponent; // Global değişkenler
let dateTime;
let stadium;

// JSON dosyasını fetch ile al
fetch("src/data/infos.json")
    .then((response) => {
        // Yanıtı kontrol et
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json(); // Yanıtı JSON olarak ayrıştır
    })
    .then((data) => {
        // JSON verisini değişkenlere ata
        data = data[0];
        opponent = data.opponent;
        dateTime = data.dateTime;

        // Verileri kullanma
        console.log("opponent:", opponent);
        console.log("dateTime:", dateTime);
    })
    .catch((error) => {
        console.error("Bir hata oluştu:", error); // Hata yönetimi
    });

// Verileri kullanmak için bir fonksiyon
function displayUserInfo() {
    if (opponent && dateTime && stadium) {
        console.log(
            `Rakip Adı: ${opponent}, Tarih: ${dateTime}`
        );
    } else {
        console.log("Veri henüz yüklenmedi.");
    }
    var deadline = new Date(dateTime);
    var c = new Clock(deadline, function () {
        alert("countdown complete");
    });
    document.body.appendChild(c.el);
    document.getElementById("opponent").innerHTML = opponent;
}

// Kullanıcı bilgilerini göstermek için fonksiyonu çağırabilirsiniz
// Bu, verilerin yüklenip yüklenmediğine bağlı olarak çalışacaktır
setTimeout(displayUserInfo, 1000); // Veri yüklenirse göster
