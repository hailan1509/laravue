<template>
  <div class="app-container">
    <div class="filter-container" style="display: flex;">

      <!-- <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button> -->
      <el-select v-model="listQuery.month" placeholder="Chọn tháng" style="margin-left: 20px">
        <el-option :key="1" :label="'Theo tháng'" :value="1" />
        <el-option :key="0" :label="'Theo ngày'" :value="0" />
      </el-select>
      <el-date-picker v-model="listQuery.date" type="date" placeholder="Chọn ngày" style="width: 20%; margin-left: 20px" />

      <v-select v-model="listQuery.phone" style="width: 30%; margin-left: 20px" :options="lstKhachHang" label="ten_kh" placeholder="Tìm kiếm khách hàng" :reduce="option => option.sdt" @input="handleInputKH" @select="handleSelectKH" />
      <el-button type="primary" style="margin-left: 20px" icon="el-icon-refresh" @click="reset()">
        Làm lại
      </el-button>
      <span style="margin-left : 20px; line-height: 36px "> <b>Tổng tiền: {{ total_money | toThousandFilter }} VNĐ</b></span>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange"
    >
      <el-table-column :label="titles.id" prop="id" sortable="custom" align="center" style="width: 5%;">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten_khach_hang" prop="ten_khach_hang" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_khach_hang }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.sdt" prop="sdt" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.sdt }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.so_luong" prop="so_luong" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.so_luong }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.tong_tien" prop="tong_tien" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.tong_tien | toThousandFilter }} VNĐ</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.created_at" prop="created_at" sortable="custom" align="center" style="width: 5%;">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | convertDateFromTimestamp }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" style="width: 30%;" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" @click="handleUpdate(row)">
            Xem chi tiết
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <h2>Danh sách chi tiết hoá đơn : {{ total_ct | toThousandFilter }} VNĐ</h2>
      <el-table
        :key="tableKey"
        v-loading="listLoading"
        :data="chi_tiets"
        border
        fit
        highlight-current-row
        style="width: 100%;"
        @sort-change="sortChange"
      >
        <el-table-column :label="titles.ten_dich_vu" prop="ten_dich_vu" sortable="custom" align="center" style="width: 5%;">
          <template slot-scope="scope">
            <span>{{ scope.row.ten_dich_vu }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.soluong" prop="soluong" sortable="custom" align="center" style="width: 10%;">
          <template slot-scope="scope">
            <span>{{ scope.row.soluong }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.gia" prop="gia" sortable="custom" align="center" style="width: 20%;">
          <template slot-scope="scope">
            <span>{{ scope.row.gia }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.gia_khuyen_mai" prop="gia_khuyen_mai" sortable="custom" align="center" style="width: 20%;">
          <template slot-scope="scope">
            <span>{{ scope.row.gia_khuyen_mai }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.thanh_tien" sortable="custom" align="center" style="width: 10%;">
          <template slot-scope="scope">
            <span>{{ parseInt(scope.row.gia_khuyen_mai) ? Math.floor((scope.row.soluong * scope.row.gia) - (scope.row.soluong * scope.row.gia) * parseInt(scope.row.gia_khuyen_mai) / 100) : (scope.row.soluong * scope.row.gia) | toThousandFilter }} VNĐ</span>
          </template>
        </el-table-column>
      </el-table>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="dialogPvVisible" title="Reading statistics">
      <el-table :data="pvData" border fit highlight-current-row style="width: 100%">
        <el-table-column prop="key" label="Channel" />
        <el-table-column prop="pv" label="Pv" />
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogPvVisible = false">{{ $t('table.confirm') }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList } from '@/api/hoa_don';
import { fetchList as listKH } from '@/api/khach_hang';
import { fetchList as getLstPhong } from '@/api/phong';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'QuanLyHopDong',
  components: { Pagination, vSelect },
  directives: { waves },
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
    const today = new Date();
    const yyyy = today.getFullYear();
    let mm = today.getMonth() + 1; // Months start at 0!
    let dd = today.getDate();

    if (dd < 10) {
      dd = '0' + dd;
    }
    if (mm < 10) {
      mm = '0' + mm;
    }
    const formattedToday = yyyy + '/' + mm + '/' + dd;
    return {
      tableKey: 0,
      list: null,
      total: 0,
      total_money: 0,
      listLoading: true,
      lstPhong: {},
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        phone: '',
        date: formattedToday,
        month: 0,
      },
      temp: {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        type: '',
        status: 'published',
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create',
      },
      dialogPvVisible: false,
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      titles: {
        id: 'Mã hoá đơn',
        ten_khach_hang: 'Tên khách hàng',
        sdt: 'Số điện thoại',
        tong_tien: 'Tổng đơn',
        so_luong: 'Số lượng dịch vụ',
        soluong: 'Số lượng',
        gia: 'Giá',
        gia_khuyen_mai: 'Chiết khấu',
        ten_dich_vu: 'Tên dịch vụ',
        created_at: 'Ngày',
        thanh_tien: 'Thành tiền',
      },
      loai_dich_vu: {
        0: 'Tiền điện',
        1: 'Tiền nước',
        2: 'Theo phòng',
        3: 'Theo số người',
      },
      months: {
        0: 'Chọn tháng',
        1: 1,
        2: 2,
        3: 3,
        4: 4,
        5: 5,
        6: 6,
        7: 7,
        8: 8,
        9: 9,
        10: 10,
        11: 11,
        12: 12,
      },
      lstKhachHang: [],
      search: {
        phone: '',
        date: '',
        month: 0,
      },
      chi_tiets: [],
      total_ct: 0,
      downloadLoading: false,
    };
  },
  watch: {
    'listQuery.date': {
      handler: function() {
        this.getList();
      },
      immediate: true,
    },
    'listQuery.month': {
      handler: function() {
        this.getList();
      },
      immediate: true,
    },
    'listQuery.phone': {
      handler: function() {
        this.getList();
      },
      immediate: true,
    },
  },
  created() {
    this.getList();
    this.getKH();
    this.getlstPhong();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data, total } = await fetchList(this.listQuery);
      this.total_money = total;
      this.list = data;
      this.total = data.length;

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    reset() {
      const today = new Date();
      const yyyy = today.getFullYear();
      let mm = today.getMonth() + 1; // Months start at 0!
      let dd = today.getDate();

      if (dd < 10) {
        dd = '0' + dd;
      }
      if (mm < 10) {
        mm = '0' + mm;
      }
      const formattedToday = yyyy + '/' + mm + '/' + dd;
      this.listQuery = {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        phone: '',
        date: formattedToday,
        month: 0,
      };
    },
    async getKH(){
      const { data } = await listKH();
      data.forEach((v, key) => {
        data[key]['ten_kh'] = v['ten'] + ' - ' + v['sdt'];
      });
      this.lstKhachHang = data;
    },
    async getlstPhong(){
      const { data } = await getLstPhong(this.listQuery);
      this.lstPhong = data;
      // this.total = data.length;
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    handleModifyStatus(row, status) {
      this.$message({
        message: 'Successful operation',
        type: 'success',
      });
      row.status = status;
    },
    sortChange(data) {
      const { prop, order } = data;
      if (prop === 'id') {
        this.sortByID(order);
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id';
      } else {
        this.listQuery.sort = '-id';
      }
      this.handleFilter();
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        ten: '',
        nam_sinh: 0,
        ngay_vao: '2022-11-24',
        ma_phong: '',
        ten_phong: '',
        que_quan: '',
        da_coc: 0,
        trang_thai: '1',
        so_nguoi_thue: 1,
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = 'create';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    // createData() {
    //   this.$refs['dataForm'].validate((valid) => {
    //     if (valid) {
    //       const tmp = new Date(this.temp.ngay_vao);
    //       this.temp.ngay_vao = tmp.getFullYear() + '-' + (parseInt(tmp.getMonth()) + 1) + '-' + tmp.getDate();
    //       createHopDong(this.temp).then((res) => {
    //         this.dialogFormVisible = false;
    //         if (res.success){
    //           this.$notify({
    //             title: 'Success',
    //             message: res.message,
    //             type: 'success',
    //             duration: 2000,
    //           });
    //           this.getList();
    //           // this.list.unshift(this.temp);
    //         } else {
    //           this.$notify({
    //             title: 'Error',
    //             message: res.message,
    //             type: 'error',
    //             duration: 2000,
    //           });
    //         }
    //       });
    //     }
    //   });
    // },
    handleUpdate(row) {
      console.log(row);
      this.chi_tiets = row.chi_tiet;
      this.total_ct = row.tong_tien;
      // this.temp = Object.assign({}, row); // copy obj
      // this.temp.trang_thai = this.temp.trang_thai.toString();
      // this.temp.timestamp = new Date(this.temp.timestamp);
      // this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      // this.$nextTick(() => {
      //   this.$refs['dataForm'].clearValidate();
      // });
    },
    // updateData() {
    //   this.$refs['dataForm'].validate((valid) => {
    //     if (valid) {
    //       const tempData = Object.assign({}, this.temp);
    //       tempData.timestamp = +new Date(tempData.timestamp); // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
    //       deleteHopDong(tempData).then(() => {
    //         for (const v of this.list) {
    //           if (v.id === this.temp.id) {
    //             const index = this.list.indexOf(v);
    //             this.list.splice(index, 1, this.temp);
    //             break;
    //           }
    //         }
    //         this.dialogFormVisible = false;
    //         this.$notify({
    //           title: 'Success',
    //           message: 'Updated successfully',
    //           type: 'success',
    //           duration: 2000,
    //         });
    //       });
    //     }
    //   });
    // },
    // handleDelete(row) {
    //   deleteHopDong(row).then((rs) => {
    //     this.$notify({
    //       title: 'Success',
    //       message: 'Deleted successfully',
    //       type: 'success',
    //       duration: 2000,
    //     });
    //     this.getList();
    //   });
    // },
    // handleFetchPv(pv) {
    //   fetchPv(pv).then(response => {
    //     this.pvData = response.data.pvData;
    //     this.dialogPvVisible = true;
    //   });
    // },
    handleDownload() {
      this.downloadLoading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['timestamp', 'title', 'type', 'importance', 'status'];
        const filterVal = ['timestamp', 'title', 'type', 'importance', 'status'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list',
        });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j]);
        } else {
          return v[j];
        }
      }));
    },
    handleInputKH(value) {
    },
    handleSelectKH(value) {
    },
  },
};
</script>
