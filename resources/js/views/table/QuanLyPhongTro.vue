<template>
  <div class="app-container">
    <div class="filter-container" style="display: flex !important;">
      <!-- <el-input v-model="listQuery.title" :placeholder="$t('table.title')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-select v-model="listQuery.importance" :placeholder="$t('table.importance')" clearable style="width: 90px" class="filter-item">
        <el-option v-for="item in importanceOptions" :key="item" :label="item" :value="item" />
      </el-select>
      <el-select v-model="listQuery.type" :placeholder="$t('table.type')" clearable class="filter-item" style="width: 130px">
        <el-option v-for="item in calendarTypeOptions" :key="item.key" :label="item.display_name+'('+item.key+')'" :value="item.key" />
      </el-select>
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button> -->
      <v-select v-model="listQuery.sdt" style="width: 30%; margin-left: 20px" :options="lstKhachHang" label="ten_kh" placeholder="Tìm kiếm khách hàng" :reduce="option => option.sdt" @input="handleInputKH" @select="handleSelectKH" />
      <!-- <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button> -->
      <!-- <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        {{ $t('table.export') }}
      </el-button>
      <el-checkbox v-model="showReviewer" class="filter-item" style="margin-left:15px;" @change="tableKey=tableKey+1">
        {{ $t('table.reviewer') }}
      </el-checkbox> -->
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
      <el-table-column :label="titles.ten_khach_hang" prop="ten_khach_hang" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_khach_hang }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.sdt" prop="sdt" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.sdt }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.created_at" prop="created_at" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ convertDate(scope.row.created_at) }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten_dich_vu" prop="ten_dich_vu" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_dich_vu }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.so_luong_combo" prop="so_luong_combo" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.so_luong_combo }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.so_luong_con_lai" prop="so_luong_con_lai" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.so_luong_con_lai > 0 ? scope.row.so_luong_con_lai:'Đã hết lượt dùng' }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" style="width: 30%;" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button v-if="row.so_luong_con_lai > 0" type="primary" size="mini" @click="modalConfirm(row)">
            Thêm
          </el-button>
          <!-- <el-button v-if="row.status!='published'" size="mini" type="success" @click="handleModifyStatus(row,'published')">
            {{ $t('table.publish') }}
          </el-button>
          <el-button v-if="row.status!='draft'" size="mini" @click="handleModifyStatus(row,'draft')">
            {{ $t('table.draft') }}
          </el-button> -->
          <el-button size="mini" type="success" @click="modalDetail(row)">
            Xem
          </el-button>
          <!-- <el-button size="small" type="success" @click="handleCreateHD(row)">
            Tạo hóa đơn
          </el-button> -->
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
    <el-dialog :visible.sync="dialogPvVisible" :title="'KH '+ ten_khach_hang + ' sử dụng ' + ten_dich_vu">
      <span>Bạn có chắc chắn ?</span>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="addCombo()">Có</el-button>
      </span>
    </el-dialog>
    <el-dialog :title="'Thông tin chi tiết combo'" :visible.sync="dialogFormVisible">
      <h2>Danh sách lần sử dụng combo</h2>
      <el-table
        :key="tableKey"
        v-loading="listLoading"
        :data="chi_tiet"
        border
        fit
        highlight-current-row
        style="width: 100%;"
      >
        <el-table-column label="Lần" type="index" style="width: 40% !important;" align="center" />
        <el-table-column :label="'Ngày'" prop="created_at" sortable="custom" align="center" style="width: 60%;">
          <template slot-scope="scope">
            <span>{{ convertDate(scope.row.created_at) }}</span>
          </template>
        </el-table-column>
      </el-table>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList as listKH } from '@/api/khach_hang';
import { fetchListCombo, createCombo } from '@/api/combo';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

const calendarTypeOptions = [
  { key: 'CN', display_name: 'China' },
  { key: 'US', display_name: 'USA' },
  { key: 'JA', display_name: 'Japan' },
  { key: 'VI', display_name: 'Vietnam' },
];

// arr to obj ,such as { CN : "China", US : "USA" }
const calendarTypeKeyValue = calendarTypeOptions.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name;
  return acc;
}, {});

export default {
  name: 'QuanLyPhongTro',
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
    typeFilter(type) {
      return calendarTypeKeyValue[type];
    },
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        sdt: '',
        is_combo: 1,
      },
      importanceOptions: [1, 2, 3],
      calendarTypeOptions,
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['published', 'draft', 'deleted'],
      showReviewer: false,
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
      dialogFormHDVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create',
        createHD: 'Tạo hóa đơn',
      },
      dialogPvVisible: false,
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      titles: {
        ten_khach_hang: 'Tên khách hàng',
        sdt: 'Điện thoại',
        created_at: 'Ngày mua',
        ten_dich_vu: 'Dịch vụ',
        so_luong_combo: 'Số lượng 1 combo',
        so_luong_con_lai: 'Còn lại',
      },
      form_hoa_don: {
        ma_phong: 0,
        so_dien: 0,
        so_nuoc: 0,
        tien_phat_sinh: 0,
        da_thanh_toan: '0',
      },
      tien_dien: 0,
      tien_nuoc: 0,
      hoadon: {},
      downloadLoading: false,
      lstKhachHang: [],
      chi_tiet_hd_id: '',
      ten_dich_vu: '',
      ten_khach_hang: '',
      chi_tiet: [],
    };
  },
  watch: {
    'listQuery.sdt': {
      handler: function() {
        this.getList();
      },
      immediate: true,
    },
  },
  created() {
    this.getList();
    this.getKH();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchListCombo(this.listQuery);
      this.list = data;
      this.total = data.length;

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    async addCombo(){
      this.dialogPvVisible = false;
      const { message } = await createCombo({ id: this.chi_tiet_hd_id });
      this.$message({
        message: message,
        type: 'success',
      });
      this.getList();
    },
    modalConfirm(row){
      this.dialogPvVisible = true;
      this.chi_tiet_hd_id = row.id;
      this.ten_dich_vu = row.ten_dich_vu;
      this.ten_khach_hang = row.ten_khach_hang;
    },
    modalDetail(row){
      this.dialogFormVisible = true;
      this.chi_tiet = row.chi_tiet;
    },
    async getKH(){
      const { data } = await listKH();
      data.forEach((v, key) => {
        data[key]['ten_kh'] = v['ten'] + ' - ' + v['sdt'];
      });
      this.lstKhachHang = data;
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
        gia: 0,
        ten_phong: '',
      };
    },
    resetFormHD(){
      this.form_hoa_don = {
        ma_phong: 0,
        ten_phong: '',
        so_dien: 0,
        so_nuoc: 0,
        tien_phat_sinh: 0,
        da_thanh_toan: '0',
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
    handleUpdate(row) {
      this.temp = Object.assign({}, row); // copy obj
      this.temp.timestamp = new Date(this.temp.timestamp);
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
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
    convertDate(date) {
      const date_arr = date.split('T');
      const date_rs = date_arr[0].split('-');
      return date_rs[2] + '-' + date_rs[1] + '-' + date_rs[0];
    },
  },
};
</script>
<style>
  .el-dialog {
    width: 30% !important;
  }
</style>
