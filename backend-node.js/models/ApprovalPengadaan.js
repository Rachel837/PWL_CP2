const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const ApprovalPengadaan = sequelize.define('ApprovalPengadaan', {
    kaprodi_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    tanggal_approval: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    draft_pengadaan_id: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    users_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'approval_pengadaan',
  timestamps: false // Ganti true jika ada created_at & updated_at
});

module.exports = ApprovalPengadaan;
