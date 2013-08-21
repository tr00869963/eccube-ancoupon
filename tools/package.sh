
declare -r PROJECT_DIR=`dirname "${0}"`"/.."
declare -r SRC_DIR="${PROJECT_DIR}/src"
declare -r ARCHIVE=${1:-../AnCoupon.tar.gz}

cd $SRC_DIR
tar czf $ARCHIVE .
echo "Build plugin archive: \"$ARCHIVE\""
