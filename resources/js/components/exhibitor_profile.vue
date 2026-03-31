<template>
    <div class="card-header" style="background-color: #808080; color: white;">
        <table style="width: 100%;">
            <tr>
                <td>Company : {{ exhibitor_profile ? exhibitor_profile.company_name : '-' }}</td>
                <td>Deadline : {{ exhibitor_profile ? exhibitor_profile.deadline : '-' }}</td>
                <td>Booth No. : {{ exhibitor_profile ? exhibitor_profile.booth_number : '-' }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            exhibitor_profile: this.globalDataExhibitor
        }
    },
    methods: {
    },
    mounted() {
    }
}
</script>
