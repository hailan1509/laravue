<template>
  <div class="app-container">
    <div class="filter-container">
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
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
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
      <el-table-column :label="titles.ma_phong" prop="id" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten_phong" prop="ten_phong" sortable="custom" align="center" style="width: 40%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_phong }}</span>
        </template>
      </el-table-column>
      <!-- <el-table-column :label="titles.gia_phong" prop="gia" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.gia | toThousandFilter }}</span>
        </template>
      </el-table-column> -->
      <!-- <el-table-column :label="titles.type" prop="gia" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.type == '1' ? 'Nam' : 'Nữ' }}</span>
        </template>
      </el-table-column> -->
      <!-- <el-table-column :label="$t('table.date')" width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.timestamp | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.title')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)">{{ row.title }}</span>
          <el-tag>{{ row.type | typeFilter }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.author')" width="110px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.author }}</span>
        </template>
      </el-table-column>
      <el-table-column v-if="showReviewer" :label="$t('table.reviewer')" width="110px" align="center">
        <template slot-scope="scope">
          <span style="color:red;">{{ scope.row.reviewer }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.importance')" width="80px">
        <template slot-scope="scope">
          <svg-icon v-for="n in +scope.row.importance" :key="n" icon-class="star" class="meta-item__icon" />
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.readings')" align="center" width="95">
        <template slot-scope="{row}">
          <span v-if="row.pageviews" class="link-type" @click="handleFetchPv(row.pageviews)">{{ row.pageviews }}</span>
          <span v-else>0</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status }}
          </el-tag>
        </template>
      </el-table-column> -->
      <el-table-column :label="$t('table.actions')" align="center" style="width: 30%;" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">
            Sửa
          </el-button>
          <!-- <el-button v-if="row.status!='published'" size="mini" type="success" @click="handleModifyStatus(row,'published')">
            {{ $t('table.publish') }}
          </el-button>
          <el-button v-if="row.status!='draft'" size="mini" @click="handleModifyStatus(row,'draft')">
            {{ $t('table.draft') }}
          </el-button> -->
          <el-button size="mini" type="danger" @click="handleDelete(row)">
            Xóa
          </el-button>
          <!-- <el-button size="small" type="success" @click="handleCreateHD(row)">
            Tạo hóa đơn
          </el-button> -->
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 400px; margin-left:50px;">
        <el-form-item :label="titles.ten_phong" prop="ten_phong">
          <el-input v-model="temp.ten_phong" />
        </el-form-item>
        <!-- <el-form-item :label="titles.gia_phong" prop="gia">
          <el-input-number v-model="temp.gia" />
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          Thoát
        </el-button>
        <el-button type="primary" @click="createData()">
          Lưu
        </el-button>
      </div>
    </el-dialog>
    <el-dialog :title="'Tạo hóa đơn tháng '+((new Date).getMonth()+1) + ' cho '+ form_hoa_don.ten_phong" :visible.sync="dialogFormHDVisible">
      <el-form ref="dataForm" :rules="rules" :model="form_hoa_don" label-position="left" label-width="150px" style="width: 500px; margin-left:50px;">
        <el-form-item label="Số điện" prop="so_dien">
          <el-input-number v-model="form_hoa_don.so_dien" /> x {{ tien_dien | toThousandFilter }} = {{ form_hoa_don.so_dien * tien_dien | toThousandFilter }}
        </el-form-item>
        <el-form-item label="Số nước" prop="so_nuoc">
          <el-input-number v-model="form_hoa_don.so_nuoc" /> x {{ tien_nuoc | toThousandFilter }} = {{ form_hoa_don.so_nuoc * tien_nuoc | toThousandFilter }}
        </el-form-item>
        <el-form-item label="Tiền phát sinh" prop="tien_phat_sinh">
          <el-input-number v-model="form_hoa_don.tien_phat_sinh" />
        </el-form-item>
        <el-form-item label="Trạng thái" prop="da_thanh_toan">
          <el-select v-model="form_hoa_don.da_thanh_toan" placeholder="Trạng thái">
            <el-option label="Đã thanh toán" value="1" />
            <el-option label="Chưa thanh toán" value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormHDVisible = false">
          Thoát
        </el-button>
        <el-button type="primary" @click="createHoaDon()">
          Lưu hóa đơn
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
import { fetchList, fetchPv, createPhong, deletePhong } from '@/api/phong';
import { fetchList as lstDV } from '@/api/dich_vu';
import { createHoaDon, getHoaDonByPhong } from '@/api/hoa_don';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
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
  components: { Pagination },
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
        ten_phong: 'Tên nhóm dịch vụ',
        ma_phong: 'Mã nhóm',
        gia_phong: 'Giá phòng',
        type: 'Loại',
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
    };
  },
  created() {
    this.getList();
    this.getDV();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchList(this.listQuery);
      this.list = data;
      this.total = data.length;

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    async getHD(ma_phong) {
      const tmp = {
        ma_phong: ma_phong,
      };
      const { data } = await getHoaDonByPhong(Object.assign({}, tmp));
      this.hoadon = data;
    },
    async getDV(){
      const { data } = await lstDV();
      for (const item of data) {
        if (item.trang_thai === 0) {
          this.tien_dien = item.gia;
        }
        if (item.trang_thai === 1) {
          this.tien_nuoc = item.gia;
        }
      }
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
    createHoaDon(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          createHoaDon(this.form_hoa_don).then((res) => {
            this.dialogFormVisible = false;
            if (res.success){
              this.$notify({
                title: 'Success',
                message: res.message,
                type: 'success',
                duration: 2000,
              });
              this.getList();
              // this.list.unshift(this.temp);
            } else {
              this.$notify({
                title: 'Error',
                message: res.message,
                type: 'error',
                duration: 2000,
              });
            }
          });
        }
      });
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          createPhong(this.temp).then((res) => {
            this.dialogFormVisible = false;
            if (res.success){
              this.$notify({
                title: 'Success',
                message: res.message,
                type: 'success',
                duration: 2000,
              });
              this.getList();
              // this.list.unshift(this.temp);
            } else {
              this.$notify({
                title: 'Error',
                message: res.message,
                type: 'error',
                duration: 2000,
              });
            }
          });
        }
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
    handleCreateHD(row) {
      this.resetFormHD();
      console.log(row);
      this.form_hoa_don.ma_phong = row.id;
      this.form_hoa_don.ten_phong = row.ten_phong;
      this.dialogStatus = 'createHD';
      this.dialogFormHDVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          tempData.timestamp = +new Date(tempData.timestamp); // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          deletePhong(tempData).then(() => {
            for (const v of this.list) {
              if (v.id === this.temp.id) {
                const index = this.list.indexOf(v);
                this.list.splice(index, 1, this.temp);
                break;
              }
            }
            this.dialogFormVisible = false;
            this.$notify({
              title: 'Success',
              message: 'Updated successfully',
              type: 'success',
              duration: 2000,
            });
          });
        }
      });
    },
    handleDelete(row) {
      deletePhong(row).then((rs) => {
        this.$notify({
          title: 'Success',
          message: 'Deleted successfully',
          type: 'success',
          duration: 2000,
        });
        this.getList();
      });
    },
    handleFetchPv(pv) {
      fetchPv(pv).then(response => {
        this.pvData = response.data.pvData;
        this.dialogPvVisible = true;
      });
    },
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
  },
};
</script>
