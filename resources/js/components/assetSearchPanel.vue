<template>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label for="input-1">Business Unit</label>
                <v-select class="form-control" placeholder="Select an Business Unit" :options="listBusinessUnit"
                    label="label" :reduce="option => option.value" v-model="businessUnitId"
                    @option:selected="changeValue('businessUnitId', businessUnitId)"></v-select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label for="input-1">Asset Group</label>
                <v-select class="form-control" placeholder="Select an Asset Group" :options="listAssetGroup"
                    label="label" :reduce="option => option.value" v-model="assetGroupId"
                    @option:selected="changeValue('assetGroupId', assetGroupId)"></v-select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label for="input-1">Asset Location</label>
                <v-select class="form-control" placeholder="Select an Asset Location" :options="listAssetLocation"
                    label="label" :reduce="option => option.value" v-model="assetLocationId"
                    @option:selected="changeValue('assetLocationId', assetLocationId)"></v-select>
            </div>
        </div>
        <div class="col-lg-3" v-if="hasValidationListener">
            <div class="form-group">
                <label for="input-1">Asset Validation</label>
                <v-select class="form-control" placeholder="Select an Asset Location" :options="listValidation"
                    label="label" :reduce="option => option.value" v-model="validation"
                    @option:selected="changeValue('barcodeValidation', validation)"></v-select>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import swalNotif from '../Utils/swalNotif';
import { getCurrentInstance } from 'vue';
import { useRoute } from 'vue-router';

export default {
    emits: ['businessUnitId', 'assetGroupId', 'assetLocationId', 'refreshTable', 'barcodeValidation'],
    data() {
        return {
            disabled: false,
            listFile: [],
            listBusinessUnit: [],
            businessUnitId: "all",
            listAssetGroup: [],
            assetGroupId: "all",
            listAssetLocation: [],
            assetLocationId: "all",
            validation: "all",
            listValidation: [
                {
                    label: 'All',
                    value: 'all'
                },
                {
                    label: 'Not Pychical Checked',
                    value: '0'
                },
                {
                    label: 'Pychical Checked',
                    value: '1'
                }
            ]
        }
    },
    methods: {
        get_data_search() {
            const vm = this;
            this.disabled = true;
            this.globalLoader.show = true;
            axios.post("/api/v1/web/asset/register/get/data", {}, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    if (res.data.data.businessUnit.length > 0) {
                        vm.listBusinessUnit = [
                            {
                                label: 'All',
                                value: 'all'
                            },
                            ...res.data.data.businessUnit.map(item => ({
                                label: item.name,
                                value: item.id
                            }))
                        ]
                    }
                    if (res.data.data.assetGroup.length > 0) {
                        vm.listAssetGroup = [
                            {
                                label: 'All',
                                value: 'all'
                            },
                            ...res.data.data.assetGroup.map(item => ({
                                label: item.name,
                                value: item.id
                            }))
                        ]
                    }
                    if (res.data.data.assetLocation.length > 0) {
                        vm.listAssetLocation = [
                            {
                                label: 'All',
                                value: 'all'
                            },
                            ...res.data.data.assetLocation.map(item => ({
                                label: item.name,
                                value: item.id
                            }))
                        ]
                    }
                    vm.$emit('assetLocationId', vm.assetLocationId);
                    vm.$emit('assetGroupId', vm.assetGroupId);
                    vm.$emit('businessUnitId', vm.businessUnitId);
                    vm.$emit('refreshTable');
                }
            }).catch(res => {
                swalNotif.error("Error Get Data!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        changeValue(target, value) {
            this.$emit(target, value);
            this.$emit('refreshTable');
        }
    },
    computed: {
        hasValidationListener() {
            const i = getCurrentInstance();
            return !!i?.vnode.props?.onBarcodeValidation;
        }
    },
    mounted() {
        this.get_data_search();
        const route = useRoute();
        if (route.query.businessUnitId) {
            this.businessUnitId = route.query.businessUnitId;
        }
        if (route.query.assetGroupId) {
            this.assetGroupId = route.query.assetGroupId;
        }
        if (route.query.assetLocationId) {
            this.assetLocationId = route.query.assetLocationId;
        }
    }
}
</script>
