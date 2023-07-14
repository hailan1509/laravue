<template>
  <div class="app-container">
    <div class="filter-container">

      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <br>
      <el-input v-model="listQuery.search" style="width : 50%" placeholder="Tìm kiếm theo tên dịch vụ" />

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
      <el-table-column :label="titles.ten_dich_vu" prop="ten_dich_vu" sortable="custom" align="center" style="width: 40%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_dich_vu }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.gia" prop="gia" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.gia | toThousandFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.trang_thai" prop="trang_thai" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.trang_thai == '1' ? 'Hiện' : 'Ẩn' }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten_khuyen_mai" prop="ten_khuyen_mai" sortable="custom" align="center" style="width: 40%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_khuyen_mai }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.gia_khuyen_mai" prop="gia_khuyen_mai" sortable="custom" align="center" style="width: 40%;">
        <template slot-scope="scope">
          <span>{{ scope.row.gia_khuyen_mai ? scope.row.gia_khuyen_mai + '%' : '' }}</span>
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
        <el-form-item :label="titles.ten_dich_vu" prop="ten_dich_vu">
          <el-input v-model="temp.ten_dich_vu" />
        </el-form-item>
        <el-form-item :label="titles.gia" prop="gia">
          <el-input-number v-model="temp.gia" />
        </el-form-item>
        <el-form-item :label="titles.trang_thai" prop="trang_thai">
          <el-select v-model="temp.trang_thai" placeholder="Trạng thái">
            <el-option label="Ẩn" value="0" />
            <el-option label="Hiện" value="1" />
          </el-select>
        </el-form-item>
        <el-form-item :label="titles.ten_khuyen_mai" prop="ten_khuyen_mai">
          <el-input v-model="temp.ten_khuyen_mai" />
        </el-form-item>
        <el-form-item :label="titles.gia_khuyen_mai" prop="gia_khuyen_mai">
          <el-input-number v-model="temp.gia_khuyen_mai" />
        </el-form-item>
        <el-form-item :label="titles.is_combo" prop="is_combo">
          <el-switch v-model="temp.is_combo" />
        </el-form-item>
        <el-form-item v-if="temp.is_combo" :label="titles.so_luong_combo" prop="so_luong_combo">
          <el-input-number v-model="temp.so_luong_combo" />
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
  </div>
</template>

<script>
import { fetchList, fetchPv, createDichVu, deleteDichVu } from '@/api/dich_vu';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'QuanLyDichVu',
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
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      titles: {
        ten_dich_vu: 'Tên dịch vụ',
        id: 'Mã dịch vụ',
        gia: 'Giá dịch vụ',
        ten_khuyen_mai: 'Tên khuyến mại',
        gia_khuyen_mai: 'Giá khuyến mại',
        trang_thai: 'Trạng thái',
        is_combo: 'Gói combo',
        so_luong_combo: 'Số lượng / combo',
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
          createDichVu(this.temp).then((res) => {
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
      this.temp.trang_thai = this.temp.trang_thai.toString();
      this.temp.timestamp = new Date(this.temp.timestamp);
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
          tempData.timestamp = +new Date(tempData.timestamp); // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          deleteDichVu(tempData).then(() => {
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
      deleteDichVu(row).then((rs) => {
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
