<template>
  <div class="app-container">
    <div class="filter-container">
      <br>
      <el-button type="succes" @click="getListBD()">
        Xem sinh nhật
      </el-button>
      <el-input v-model="listQuery.search" style="width : 50%" placeholder="Tìm kiếm theo tên khách hàng" />

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
      <el-table-column :label="titles.id" prop="id" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten" prop="ten" sortable="custom" align="center" style="width: 40%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.sdt" prop="sdt" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.sdt }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" style="width: 30%;" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">
            {{ $t('table.edit') }}
          </el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">
            {{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 400px; margin-left:50px;">
        <el-form-item :label="titles.ten" prop="ten">
          <el-input v-model="temp.ten" />
        </el-form-item>
        <el-form-item :label="titles.sdt" prop="sdt">
          <el-input v-model="temp.sdt" :max="10" :min="9" />
        </el-form-item>
        <el-form-item :label="titles.ngay_sinh" prop="ngay_sinh">
          <el-date-picker v-model="temp.ngay_sinh" type="date" format="dd/MM/yyyy" value-format="yyyy-MM-dd" placeholder="Ngày sinh" style="width: 100%;" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" @click="createData()">
          Lưu
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
    <el-dialog :title="'Khách hàng sắp đến sinh nhật'" :visible.sync="dialogFormBDVisible">
      <el-table
        :key="tableKey"
        v-loading="listLoading"
        :data="listBirthDay"
        border
        fit
        highlight-current-row
        style="width: 100%;"
      >
        <el-table-column :label="titles.id" prop="id" sortable="custom" align="center" style="width: 10%;">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.ten" prop="ten" sortable="custom" align="center" style="width: 40%;">
          <template slot-scope="scope">
            <span>{{ scope.row.ten }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.ngay_sinh" prop="ngay_sinh" sortable="custom" align="center" style="width: 40%;">
          <template slot-scope="scope">
            <span>{{ scope.row.ngay_sinh | convertDateFromTimestamp }}</span>
          </template>
        </el-table-column>
        <el-table-column :label="titles.sdt" prop="sdt" sortable="custom" align="center" style="width: 20%;">
          <template slot-scope="scope">
            <span>{{ scope.row.sdt }}</span>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, createKH, deleteKH, listBirthDay } from '@/api/khach_hang';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'QuanLyKhachHang',
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
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      listBirthDay: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        search: '',
      },
      temp: {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        type: '',
        status: 'published',
        is_combo: false,
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create',
      },
      dialogPvVisible: false,
      dialogFormBDVisible: false,
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
        sdt: [{ regex: '(84|0[3|5|7|8|9])+([0-9]{8})\b', message: 'Số điện thoại không đúng định dạng', trigger: 'blur' }],
      },
      titles: {
        ten: 'Tên khách hàng',
        id: 'Mã khách hàng',
        sdt: 'Số điện thoại',
        ngay_sinh: 'Ngày sinh',
      },
      loai_dich_vu: {
        0: 'Ẩn',
        1: 'Hiện',
      },
      downloadLoading: false,
    };
  },
  watch: {
    'listQuery.search': {
      handler: function() {
        this.getList();
      },
      immediate: true,
    },
  },
  created() {
    this.getList();
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
    async getListBD() {
      this.listLoading = true;
      const { data } = await listBirthDay(this.listQuery);
      this.listBirthDay = data;
      this.dialogFormBDVisible = true;

      // Just to simulate the time of the request
      this.listLoading = false;
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
        ten_dich_vu: '',
        ten_khuyen_mai: '',
        gia_khuyen_mai: 0,
        trang_thai: '1',
        is_combo: false,
        so_luong_combo: 0,
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
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          if (this.temp.sdt.length !== 10) {
            this.$notify({
              title: 'Error',
              message: 'Số điện thoại không đúng định dạng!',
              type: 'error',
              duration: 2000,
            });
            return;
          }
          createKH(this.temp).then((res) => {
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
      console.log(row);
      this.temp = Object.assign({}, row); // copy obj
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          deleteKH(tempData).then(() => {
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
      deleteKH(row).then((rs) => {
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
