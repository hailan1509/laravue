<template>
  <div class="app-container">
    <div class="filter-container" style="display: flex;">

      <!-- <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button> -->
      <el-date-picker v-model="temp.ngay_vao" type="date" placeholder="Chọn ngày" style="width: 20%; margin-left: 20px" />
      <el-select v-model="temp.ma_phong" placeholder="Chọn tháng" style="margin-left: 20px">
        <el-option v-for="(key, item) in months" :key="key" :label="item" :value="key" />
      </el-select>

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
      <el-table-column :label="titles.ten" prop="ten" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ten_phong" prop="ten_phong" sortable="custom" align="center" style="width: 20%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ten_phong }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.nam_sinh" prop="nam_sinh" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.nam_sinh }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.da_coc" prop="da_coc" sortable="custom" align="center" style="width: 10%;">
        <template slot-scope="scope">
          <span>{{ scope.row.da_coc | toThousandFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="titles.ngay_vao" prop="ngay_vao" sortable="custom" align="center" style="width: 5%;">
        <template slot-scope="scope">
          <span>{{ scope.row.ngay_vao | convertDate }}</span>
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
        <el-form-item :label="titles.nam_sinh" prop="nam_sinh">
          <el-input-number v-model="temp.nam_sinh" />
        </el-form-item>
        <el-form-item :label="titles.que_quan" prop="que_quan">
          <el-input v-model="temp.que_quan" />
        </el-form-item>
        <el-form-item :label="titles.ma_phong" prop="ma_phong">
          <el-select v-model="temp.ma_phong" placeholder="Chọn phòng">
            <el-option v-for="item in lstPhong" :key="item.id" :label="item.ten_phong" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item :label="titles.da_coc" prop="da_coc">
          <el-input-number v-model="temp.da_coc" />
        </el-form-item>
        <el-form-item :label="titles.ngay_vao" prop="ngay_vao">
          <el-date-picker v-model="temp.ngay_vao" type="date" placeholder="Chọn ngày vào" style="width: 100%;" />
        </el-form-item>
        <el-form-item :label="titles.so_nguoi_thue" prop="so_nguoi_thue">
          <el-input-number v-model="temp.so_nguoi_thue" />
        </el-form-item>
        <el-form-item :label="titles.trang_thai" prop="trang_thai">
          <el-select v-model="temp.trang_thai" placeholder="Trạng thái">
            <el-option label="Hoạt động" value="1" />
            <el-option label="Không hoạt động" value="0" />
          </el-select>
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
import { fetchList, fetchPv, createHopDong, deleteHopDong } from '@/api/hop_dong';
import { fetchList as getLstPhong } from '@/api/phong';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'QuanLyHopDong',
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
      lstPhong: {},
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
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
        tong_tien: 'Tổng đơn',
        so_luong: 'Số lượng dịch vụ',
        created_date: 'Ngày',
      },
      loai_dich_vu: {
        0: 'Tiền điện',
        1: 'Tiền nước',
        2: 'Theo phòng',
        3: 'Theo số người',
      },
      months: {
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
      downloadLoading: false,
    };
  },
  created() {
    this.getList();
    this.getlstPhong();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchList(this.listQuery);
      this.list = data;
      console.log(this.list);
      this.total = data.length;

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    async getlstPhong(){
      const { data } = await getLstPhong(this.listQuery);
      this.lstPhong = data;
      console.log(this.lstPhong);
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
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tmp = new Date(this.temp.ngay_vao);
          this.temp.ngay_vao = tmp.getFullYear() + '-' + (parseInt(tmp.getMonth()) + 1) + '-' + tmp.getDate();
          createHopDong(this.temp).then((res) => {
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
          deleteHopDong(tempData).then(() => {
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
      deleteHopDong(row).then((rs) => {
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
