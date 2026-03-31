<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asset File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        @click="$emit('isShowModal', false)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="documentType === 'document'">
                        <div class="table-responsive" id="table_container">
                            <table class="table table-bordered table-striped" style="width: 100%" id="tableDocument">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;">#</th>
                                        <th>File Name</th>
                                        <th>Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="listFile.length > 0">
                                        <tr v-for="(item, index) in listFile" :key="index">
                                            <td style="width: 2%;">{{ index + 1 }}</td>
                                            <td style="width: 70%;overflow-wrap: anywhere; white-space: normal;"><a
                                                    :href="'/asset/file/' + item.id">{{ item.name }}</a></td>
                                            <td style="width: 28%;">{{ item.date }}</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr style="text-align: center;">
                                            <td colspan="4">Document Not Found</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="documentType === 'image'">
                        <Carousel>
                            <Slide v-for="(item, index) in listFile" :key="index">
                                <div class="carousel__item">
                                    <a :href="'/asset/file/' + item.id" target="Blank"><img
                                            :src="'/asset/file/' + item.id" alt="" style="width: 550px;"></a>
                                </div>
                            </Slide>
                            <template #addons>
                                <Navigation />
                                <Pagination />
                            </template>
                        </Carousel>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-inverse-primary" data-dismiss="modal"
                        @click="$emit('isShowModal', false)"><i class="fa fa-times"></i>
                        Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import 'vue3-carousel/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import swalNotif from '../Utils/swalNotif';

export default {
    props: ['assetId', 'documentType', 'modalId'],
    emits: ['isShowModal'],
    components: {
        Carousel,
        Slide,
        Pagination,
        Navigation
    },
    data() {
        return {
            disabled: false,
            listFile: [],
            carouselConfig: {
                itemsToShow: 2.5,
                wrapAround: true
            }
        }
    },
    methods: {
        getFile() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/register/file/type/get", {
                id: this.assetId,
                type: this.documentType
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    this.listFile = res.data.data.map(item => ({
                        id: item.id,
                        name: item.file_name,
                        type: item.type,
                        date: item.created_at_formatted,
                    }));
                    $("#" + vm.modalId).modal({ backdrop: 'static', keyboard: false });
                }
                else {
                    swalNotif.error(res.data.message);
                    vm.$emit('isShowModal', false);
                }
            }).catch(err => {
                swalNotif.error(err.response.data.message);
            }).finally(function () {
                vm.globalLoader.show = false;
            })
        }
    },
    mounted() {
        this.getFile();
    }
}
</script>
