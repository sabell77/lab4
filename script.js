document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. Dynamic City Selection based on State
    // -------------------------------------------------------------
    const citiesByState = {
        "Johor": ["Johor Bahru", "Iskandar Puteri", "Batu Pahat", "Kluang", "Muar", "Kulai", "Segamat", "Pontian", "Kota Tinggi", "Mersing"],
        "Kedah": ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi", "Baling", "Jitra", "Pendang", "Yan"],
        "Kelantan": ["Kota Bharu", "Tanah Merah", "Machang", "Pasir Mas", "Gua Musang", "Tumpat", "Kuala Krai", "Pasir Puteh"],
        "Melaka": ["Melaka City", "Alor Gajah", "Jasin", "Ayer Keroh", "Masjid Tanah"],
        "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai", "Jempol", "Tampin", "Kuala Pilah", "Rembau"],
        "Pahang": ["Kuantan", "Temerloh", "Bentong", "Mentakab", "Pekan", "Cameron Highlands", "Raub", "Jerantut"],
        "Penang": ["George Town", "Butterworth", "Seberang Perai", "Bayan Lepas", "Bukit Mertajam", "Nibong Tebal"],
        "Perak": ["Ipoh", "Taiping", "Teluk Intan", "Manjung", "Sitiawan", "Batu Gajah", "Kampar", "Kuala Kangsar", "Lumut"],
        "Perlis": ["Kangar", "Arau", "Kuala Perlis", "Padang Besar"],
        "Sabah": ["Kota Kinabalu", "Sandakan", "Tawau", "Keningau", "Lahad Datu", "Penampang", "Papar", "Ranau", "Semporna", "Kudat"],
        "Sarawak": ["Kuching", "Miri", "Sibu", "Bintulu", "Limbang", "Sarikei", "Sri Aman", "Kapit", "Kota Samarahan"],
        "Selangor": ["Shah Alam", "Petaling Jaya", "Subang Jaya", "Klang", "Ampang Jaya", "Kajang", "Selayang", "Rawang", "Sepang"],
        "Terengganu": ["Kuala Terengganu", "Chukai (Kemaman)", "Dungun", "Besut", "Marang", "Kuala Berang"],
        "Wilayah Persekutuan Kuala Lumpur": ["Kuala Lumpur", "Cheras", "Kepong", "Bukit Bintang", "Setapak", "Wangsa Maju", "Bangsar", "Brickfields"],
        "Wilayah Persekutuan Labuan": ["Victoria (Labuan)", "Ranch-Ranch", "Kampung Sungai Bedaun"],
        "Wilayah Persekutuan Putrajaya": ["Putrajaya"]
    };

    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');

    stateSelect.addEventListener('change', function () {
        const selectedState = this.value;
        citySelect.innerHTML = '<option value="" disabled selected>-- Select City --</option>';

        if (citiesByState[selectedState]) {
            citySelect.disabled = false;
            citiesByState[selectedState].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        } else {
            citySelect.disabled = true;
        }
    });

    // -------------------------------------------------------------
    // 2. Real-Time Password Validation & Style Manipulation
    // -------------------------------------------------------------
    const pwdInput = document.getElementById('password');
    const reqList = {
        length: { el: document.getElementById('req-length'), regex: /.{8,}/, text: 'At least 8 characters' },
        lower:  { el: document.getElementById('req-lower'),  regex: /[a-z]/, text: 'At least 1 lowercase letter' },
        upper:  { el: document.getElementById('req-upper'),  regex: /[A-Z]/, text: 'At least 1 uppercase letter' },
        num:    { el: document.getElementById('req-num'),    regex: /\d/,    text: 'At least 1 number' },
        spec:   { el: document.getElementById('req-spec'),   regex: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/, text: 'At least 1 special character (!@#$%^&*)' }
    };

    pwdInput.addEventListener('input', function() {
        const val = this.value;

        Object.values(reqList).forEach(req => {
            const isValid = req.regex.test(val);
            if (isValid) {
                req.el.classList.add('valid');
                req.el.textContent = '✔ ' + req.text;
            } else {
                req.el.classList.remove('valid');
                req.el.textContent = '✖ ' + req.text;
            }
        });
    });

    // -------------------------------------------------------------
    // 3. Form Validation & Dynamic Feedback Display
    // -------------------------------------------------------------
    const form = document.getElementById('registrationForm');
    const feedback = document.getElementById('formFeedback');

    form.addEventListener('submit', function(e) {
        const val = pwdInput.value;
        const allPasswordValid = Object.values(reqList).every(req => req.regex.test(val));

        if (!allPasswordValid) {
            e.preventDefault();
            feedback.style.display = 'block';
            feedback.style.backgroundColor = '#ffebee';
            feedback.style.borderColor = '#c62828';
            feedback.style.color = '#b71c1c';
            feedback.innerHTML = `<strong>Error!</strong> Please satisfy all password requirements before submitting.`;
            return;
        }

        const fname = document.getElementById('fname').value;
        const email = document.getElementById('email').value;

        // Apply dynamic styling changes on success
        feedback.style.display = 'block';
        feedback.style.backgroundColor = '#e8f5e9';
        feedback.style.borderColor = '#2e7d32';
        feedback.style.color = '#1b5e20';
        feedback.innerHTML = `<strong>Registration Successful!</strong><br>Thank you, ${fname}. A confirmation email will be sent to <em>${email}</em>.`;
    });

    // Reset feedback on form reset
    form.addEventListener('reset', function() {
        feedback.style.display = 'none';
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="" disabled selected>-- Select State First --</option>';

        Object.values(reqList).forEach(req => {
            req.el.classList.remove('valid');
            req.el.textContent = '✖ ' + req.text;
        });
    });
});