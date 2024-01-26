const DateMixin = {
    data() {
        return {
            monthNames: ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ],
        };
    },
    methods: {
        formatDate(str) {
            if (!str) return;

            let date = new Date(str);
            let day = date.getDate();
            let month = date.getMonth();
            let year = date.getFullYear();

            return day + ' ' + this.monthNames[month] + ', ' + year;
        },
        formatTime(str) {
            if (!str) return;
            let date = new Date(str);
            let hour = (date.getHours()).toString().padStart(2, '0');
            let min = (date.getMinutes()).toString().padStart(2, '0');

            return hour + ':' + min;
        },
        formatDateTime(str) {
            const date = this.formatDate(str);
            const time = this.formatTime(str);

            return date + '<br> at ' + time;
        },
    }
};

export default DateMixin;