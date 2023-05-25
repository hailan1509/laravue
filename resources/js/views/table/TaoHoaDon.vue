<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="container">
        <div class="row line">
          <h1>Tạo hóa đơn</h1>
        </div>
      </div>
      <el-form ref="form" :model="form" label-width="200px">
        <el-form-item label="Thông tin khách hàng">
          <el-col :span="11">
            <v-select v-model="form.phone" :options="lstKhachHang" label="ten_kh" placeholder="Tìm kiếm khách hàng" :reduce="option => option.sdt" @input="handleInputKH" @select="handleSelectKH" />
            <!-- <el-input v-model="form.phone" placeholder="Số điện thoại" /> -->
          </el-col>
          <el-col :span="1">&nbsp;
            <!-- <el-input v-model="form.name" placeholder="Tên khách hàng" /> -->
          </el-col>
          <el-col :span="5">
            <el-input v-model="form.name" placeholder="Tên khách hàng" />
          </el-col>
          <el-col :span="1">&nbsp;
            <!-- <el-input v-model="form.name" placeholder="Tên khách hàng" /> -->
          </el-col>
          <el-col :span="5">
            <el-input v-model="form.phone" placeholder="Số điện thoại" />
          </el-col>
        </el-form-item>
        <el-form-item label="Dịch vụ khách hàng">
          <el-col :span="11">
            <!-- <el-input v-model="form.name" placeholder="Tên khách hàng" /> -->
            <v-select v-model="dv_id" :options="lstDichVu" label="ten_dich_vu" placeholder="Tìm kiếm dịch vụ" :reduce="option => option.id" @input="handleInput" @select="handleSelect" />
          </el-col>
          <el-col :span="2">&nbsp;
            <!-- <el-input v-model="form.name" placeholder="Tên khách hàng" /> -->
          </el-col>
          <el-col :span="11">
            <el-button type="success" @click="add_dv(dv_id)">
              Thêm dịch vụ
            </el-button>
          </el-col>
        </el-form-item>
      </el-form>
      <table class="service-table text-center">
        <thead class="text-center">
          <th width="20%">Tên dịch vụ</th>
          <th width="15%">Số lượng</th>
          <th width="15%">Giá</th>
          <th width="20%">Chiết khấu</th>
          <th width="15%">Thành tiền</th>
          <th width="15%">Hành động</th>
        </thead>
        <tbody class="text-center">
          <tr v-for="item of newData" :key="item.id">
            <td>{{ item.ten_dich_vu_km }}</td>
            <td><el-input-number v-model="item.soluong" :min="min" placeholder="Số lượng" style="width: 100%" /></td>
            <td>{{ item.gia | toThousandFilter }}</td>
            <td>{{ item.gia_khuyen_mai ? item.gia_khuyen_mai + '%' : '' }}</td>
            <td>{{ parseInt(item.gia_khuyen_mai) ? Math.floor((item.soluong * item.gia) - (item.soluong * item.gia) * parseInt(item.gia_khuyen_mai) / 100) : item.soluong * item.gia | toThousandFilter }}</td>
            <td><i class="el-icon-error" @click="deleteDV(item.id)" /></td>
          </tr>
          <tr>
            <td colspan="4" class="text-right"><b>Tổng tiền</b></td>
            <td colspan="2"><b>{{ tongTien() | toThousandFilter }} VNĐ</b></td>
          </tr>
        </tbody>
      </table>
      <!-- <el-form-item label="Chuyển khoản"> -->
      <el-form label-width="200px">
        <el-form-item label="Chuyển khoản">
          <el-switch v-model="form.delivery" />
        </el-form-item>
      </el-form>
      <el-button type="primary" :disabled="!form.phone || newData.length == 0" style="width:100%" @click="save()">
        Tạo hóa đơn
      </el-button>
      <!-- </el-form-item> -->
    </div>
  </div>
</template>
<script>
import { fetchList } from '@/api/dich_vu';
import { createHoaDon } from '@/api/hoa_don';
import { fetchList as listKH } from '@/api/khach_hang';
import waves from '@/directive/waves'; // Waves directive
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
  name: 'TaoHoaDon',
  directives: { waves },
  components: {
    'v-select': vSelect,
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      form: {
        phone: '',
        name: '',
        delivery: false,
      },
      lstDichVu: [],
      dv_id: '',
      newData: [],
      lstKhachHang: [],
      min: 0,
    };
  },
  mounted() {
    // $(this.$refs.selectElement).select2();
  },
  created() {
    this.getList();
    this.loadKH();
  },
  methods: {
    async loadKH() {
      this.listLoading = true;
      const { data } = await listKH();
      data.forEach((v, key) => {
        data[key]['ten_kh'] = v['ten'] + ' - ' + v['sdt'];
      });
      this.lstKhachHang = data;
      this.listLoading = false;
    },
    async getList() {
      this.listLoading = true;
      const { data } = await fetchList();
      data.forEach((v, key) => {
        data[key]['ten_dich_vu_km'] = v['ten_dich_vu'];
        if (v['ten_khuyen_mai']) {
          data[key]['ten_dich_vu_km'] = v['ten_dich_vu'] + ' ( ' + v['ten_khuyen_mai'] + ' )';
        }
      });
      this.lstDichVu = data;
      data.forEach(v => {
        v['soluong'] = 1;
      });

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    handleInput(value) {
      // console.log(value);
    },
    handleSelect(value) {
      // console.log(value);
    },
    handleInputKH(value) {
      const item = this.lstKhachHang.find(v => v.sdt === value);
      if (!item) {
        return;
      }
      this.form.name = item.ten;
    },
    handleSelectKH(value) {
    },
    add_dv(dv_id) {
      const item = this.newData.find(x => x.id === dv_id);
      if (!item) {
        const addItem = this.lstDichVu.find(x => x.id === dv_id);

        if (!addItem) {
          return;
        }
        this.newData = [
          ...this.newData,
          {
            ...addItem,
            soluong: 1,
          },
        ];
      } else {
        item.soluong += 1;
      }
    },
    deleteDV(dv_id) {
      const index = this.newData.findIndex(x => x.id === dv_id);
      this.newData.splice(index, 1);
    },
    tongTien() {
      let tt = 0;
      this.newData.forEach(item => {
        tt += parseInt(item.gia_khuyen_mai) ? Math.floor((item.soluong * item.gia) - (item.soluong * item.gia) * parseInt(item.gia_khuyen_mai) / 100) : (item.soluong * item.gia);
      });
      return tt;
    },
    async save() {
      if (this.form.name.length === 0 || this.form.phone.length === 0 || this.newData.length === 0) {
        this.$notify({
          title: 'Cảnh báo',
          message: 'Hãy nhập đầy đủ thông tin!',
          type: 'danger',
          duration: 2000,
        });
        return;
      }
      const tmp = {
        data: this.newData,
        total: this.tongTien(),
      };
      const params = {
        ... this.form,
        ...tmp,
      };
      const { success, message } = await createHoaDon(params);
      if (success) {
        this.$notify({
          title: 'Thông báo',
          message: message,
          type: 'success',
          duration: 2000,
        });
      } else {
        this.$notify({
          title: 'Cảnh báo',
          message: message,
          type: 'danger',
          duration: 2000,
        });
      }
    },
  },
};
</script>
<style scoped>
  .line{
    text-align: center;
  }
  .service-table {
  width: 100%;
  border-collapse: collapse;
}

.service-table th,
.service-table td {
  padding: 12px;
  text-align: center;
  border: 1px solid #ddd;
}

.service-table th {
  background-color: #f2f2f2;
  font-weight: bold;
  color: #333;
}

.service-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.service-table tbody tr:hover {
  background-color: #e6e6e6;
}

.service-table td:last-child {
  text-align: center;
}

.service-table .delete-button {
  padding: 6px 10px;
  background-color: #e74c3c;
  color: #fff;
  border: none;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.service-table .delete-button:hover {
  background-color: #c0392b;
}

.service-table .delete-button:active {
  background-color: #962d22;
}
.text-left {
  text-align: left;
}
.text-right {
  text-align: right !important;
}
.text-center {
  text-align: center;
}
</style>
